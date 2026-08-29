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

use LMS3\Lms3h5p\Domain\Repository\LibraryRepository;
use LMS3\Lms3h5p\Service\H5PIntegrationService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

/**
 * Library Controller
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class LibraryController extends AbstractModuleController
{
    protected ModuleTemplate $moduleTemplate;

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly IconFactory $iconFactory,
        private readonly LibraryRepository $libraryRepository,
        private readonly H5PIntegrationService $h5pIntegrationService
    ) {}

    protected function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
        $actions = ['createAction', 'updateAction', 'deleteAction'];

        if (!in_array($this->actionMethodName, $actions, true)) {
            $this->generateMenu($this->moduleTemplate);
            $this->registerDocheaderButtons();
        }
    }

    public function indexAction(): ResponseInterface
    {
        $libraries = $this->libraryRepository->findAll();

        $this->moduleTemplate->assign('libraries', $libraries);

        return $this->moduleTemplate->renderResponse('Library/Index');
    }

    public function showAction(int $library): ResponseInterface
    {
        $library = $this->libraryRepository->findByUid($library);

        $this->moduleTemplate->assignMultiple([
            'library' => $library,
            'timeFormat' => 'hh:mm',
            'dateFormat' => 'y-m-d',
        ]);

        return $this->moduleTemplate->renderResponse('Library/Show');
    }

    public function deleteAction(int $library): ResponseInterface
    {
        $library = $this->libraryRepository->findByUid($library);

        $this->h5pIntegrationService->getH5PCoreInstance()->deleteLibrary($library->toStdClass());

        $this->addFlashMessage(
            sprintf(
                $this->translate('libraryDeletedMessage'),
                $library->getTitle()
            ),
            $this->translate('libraryDeleted')
        );

        return new ForwardResponse('index');
    }

    public function refreshContentTypeCacheAction(): ResponseInterface
    {
        $h5pCoreInstance = $this->h5pIntegrationService->getH5PCoreInstance();
        if ($h5pCoreInstance->updateContentTypeCache() === false) {
            $this->addFlashMessage(
                $this->translate('h5pHubNotRespondedErrorMessage'),
                '',
                ContextualFeedbackSeverity::ERROR
            );
        }
        $this->addFlashMessage($this->translate('contentTypeCachedRefreshedMessage'));

        return new ForwardResponse('index');
    }

    protected function registerDocheaderButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        if ($this->actionMethodName !== 'indexAction') {
            $uri = $this->uriBuilder->uriFor('index');
            $title = $this->translate('back');
            $icon = $this->iconFactory
                ->getIcon('actions-view-go-back', IconSize::SMALL);
        } else {
            $uri = $this->uriBuilder->uriFor('new', null, 'Content');
            $title = $this->translate('createNewContent');
            $icon = $this->iconFactory
                ->getIcon('actions-document-new', IconSize::SMALL);
        }

        $button = $buttonBar->makeLinkButton()
            ->setHref($uri)
            ->setTitle($title)
            ->setIcon($icon);

        $buttonBar->addButton($button);
    }
}
