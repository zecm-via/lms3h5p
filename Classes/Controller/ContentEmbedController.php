<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Controller;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2019 LEARNTUBE! GbR - Contact: mail@learntube.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use Doctrine\DBAL\ArrayParameterType;
use LMS3\Lms3h5p\Service\ContentService;
use LMS3\Lms3h5p\Service\FlexFormService;
use LMS3\Lms3h5p\Service\H5PIntegrationService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\ConsumableString;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Content Embed Controller
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class ContentEmbedController extends ActionController
{
    private const string CONTENT_TYPE = 'lms3h5p_pi1';
    protected string $nonce;

    public function __construct(
        protected readonly Context $context,
        protected readonly PageRenderer $pageRenderer,
        protected readonly H5PIntegrationService $h5pIntegrationService,
        protected readonly ContentService $contentService,
        protected readonly ConnectionPool $connectionPool
    ) {}

    public function indexAction(): ResponseInterface
    {
        $nonceAttribute = $this->request->getAttribute('nonce');
        if ($nonceAttribute instanceof ConsumableString) {
            $this->nonce = $nonceAttribute->consume();
        }

        $this->addScriptAndStyles();

        $contentId = (int)$this->settings['contentId'];
        if (empty($contentId)) {
            return $this->htmlResponse();
        }

        $content = $this->contentService->findByUid($contentId);
        if ($content === null) {
            return $this->htmlResponse();
        }

        $this->view->assign('content', $content);

        return $this->htmlResponse();
    }

    /**
     * Set content element scripts and styles
     */
    protected function addScriptAndStyles(): void
    {
        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('tt_content');

        $languageId = $this->context->getPropertyFromAspect('language', 'id');

        $query = $queryBuilder->select('pi_flexform')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('cType', $queryBuilder->createNamedParameter(self::CONTENT_TYPE)),
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($this->request->getAttribute('frontend.page.information')->getId())),
                $queryBuilder->expr()->in('sys_language_uid', $queryBuilder->createNamedParameter([0, $languageId], ArrayParameterType::INTEGER)),
            )
            ->orderBy('sorting')
            ->executeQuery();

        $h5pInstances = $query->fetchAllAssociative();
        if (count($h5pInstances) === 0) {
            return;
        }

        $ffs = GeneralUtility::makeInstance(FlexFormService::class);
        $contentIds = [];
        foreach ($h5pInstances as $instance) {
            $flex = $ffs->convertFlexFormContentToArray($instance['pi_flexform']);
            $contentIds[] = $flex['settings']['contentId'];
        }

        $h5pIntegrationSettings = $this->h5pIntegrationService->getH5PSettings($this->uriBuilder, array_filter($contentIds));
        $mergedScripts = array_unique($this->h5pIntegrationService->getMergedScripts($h5pIntegrationSettings));
        $mergedStyles = array_unique($this->h5pIntegrationService->getMergedStyles($h5pIntegrationSettings));

        $h5pIntegrationSettingsJs = 'window.H5PIntegration = ' . json_encode($h5pIntegrationSettings) . ';';

        if (!empty($this->nonce)) {
            $this->pageRenderer->addJsInlineCode('H5PSettings', $h5pIntegrationSettingsJs, true, false, true);
        } else {
            $this->pageRenderer->addJsInlineCode('H5PSettings', $h5pIntegrationSettingsJs);
        }

        /**
         * Add H5P CSS files
         */
        foreach ($mergedStyles as $style) {
            $this->pageRenderer->addCssFile(
                file: $style,
                compress: false,
                excludeFromConcatenation: true
            );
        }

        /**
         *  Add H5P javascript files
         */
        foreach ($mergedScripts as $script) {
            $this->pageRenderer->addJsFile($script);
        }
    }
}
