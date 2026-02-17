<?php
declare(strict_types = 1);

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

use LMS3\Lms3h5p\Service\ContentService;
use LMS3\Lms3h5p\Service\H5PIntegrationService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

/**
 * ContentController
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
#[Controller]
class ContentController extends AbstractModuleController
{
    protected ModuleTemplate $moduleTemplate;

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly IconFactory $iconFactory,
        private readonly H5PIntegrationService $h5pIntegrationService,
        private readonly ContentService $contentService
    ){
    }

    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
        $actions = ['createAction', 'updateAction', 'deleteAction'];

        if (!in_array($this->actionMethodName, $actions)) {
            $this->generateMenu($this->moduleTemplate);
            $this->registerDocheaderButtons();
        }
    }

    public function indexAction(): ResponseInterface
    {
        $this->setStoragePid();
        $contents = $this->contentService->findAll();

        $this->moduleTemplate->assignMultiple([
            'contents' => $contents,
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'pid' => empty(GeneralUtility::_GP('id'))
        ]);

        return $this->moduleTemplate->renderResponse('Content/Index');
    }

    public function newAction(): ResponseInterface
    {
        $h5pIntegrationSettings = $this->h5pIntegrationService->getSettingsWithEditor($this->uriBuilder);

        $this->moduleTemplate->assignMultiple([
            'h5pSettings' => json_encode($h5pIntegrationSettings),
            'scripts' => $h5pIntegrationSettings['core']['scripts'],
            'styles' => $h5pIntegrationSettings['core']['styles'],
            'pid' => empty(GeneralUtility::_GET('id')),
            'parameters' => ''
        ]);

        return $this->moduleTemplate->renderResponse('Content/New');
    }

    public function createAction(): ResponseInterface
    {
        $this->setStoragePid();

        $library = $this->request->getArgument('library');
        $parameters = $this->request->getArgument('parameters');
        $options = $this->request->getArgument('options');

        $content = $this->contentService->handleCreateOrUpdate($library, $parameters, null, $options);
        if ($content === null) {
            $this->showH5pErrorMessages();
            return new ForwardResponse('new');
        } else {
            $this->addFlashMessage(
                sprintf(
                    $this->translate('contentCreatedMessage'),
                    $content->getTitle()
                ),
                $this->translate('contentCreated')
            );

            return (new ForwardResponse('show'))->withArguments(['content' => $content->getUid()]);
        }
    }

    public function showAction(int $content): ResponseInterface
    {
        $this->setStoragePid();

        $content = $this->contentService->findByUid($content);
        $h5pIntegrationSettings = $this->h5pIntegrationService->getH5PSettings(
            $this->uriBuilder,
            [
                $content->getUid()
            ]
        );

        $this->moduleTemplate->assignMultiple([
            'content' => $content,
            'h5pSettings' => json_encode($h5pIntegrationSettings),
            'scripts' => $this->h5pIntegrationService->getMergedScripts($h5pIntegrationSettings),
            'styles' => $this->h5pIntegrationService->getMergedStyles($h5pIntegrationSettings),
        ]);

        return $this->moduleTemplate->renderResponse('Content/Show');
    }

    public function editAction(int $content): ResponseInterface
    {
        $content = $this->contentService->findByUid($content);
        $h5pIntegrationSettings = $this->h5pIntegrationService->getSettingsWithEditor(
            $this->uriBuilder,
            $content->getUid()
        );
        $metadata = (object)['title' => $content->getTitle(), 'license' => $content->getLicense()];
        $parameters = '{"params":' . $content->getFiltered() . ', "metadata":' . json_encode($metadata) . '}';
        $options = $this->h5pIntegrationService->getH5PCoreInstance()->getDisplayOptionsForEdit($content->getDisable());

        $this->moduleTemplate->assignMultiple([
            'h5pSettings' => json_encode($h5pIntegrationSettings),
            'scripts' => $h5pIntegrationSettings['core']['scripts'],
            'styles' => $h5pIntegrationSettings['core']['styles'],
            'content' => $content,
            'parameters' => $parameters,
            'options' => $options,
        ]);

        return $this->moduleTemplate->renderResponse('Content/Edit');
    }

    public function updateAction(): ResponseInterface
    {
        $library = $this->request->getArgument('library');
        $parameters = $this->request->getArgument('parameters');
        $contentId = $this->request->getArgument('contentId');
        $options = $this->request->getArgument('options');

        $content = $this->contentService->handleCreateOrUpdate($library, $parameters, $contentId, $options);
        if (null === $content) {
            $this->showH5pErrorMessages();
            return new ForwardResponse('index');
        } else {
            $this->addFlashMessage(
                sprintf(
                    $this->translate('contentUpdatedMessage'),
                    $content->getTitle()
                ),
                $this->translate('contentUpdated')
            );

            return (new ForwardResponse('show'))->withArguments(['content' => $content->getUid()]);
        }
    }

    public function deleteAction(int $content): ResponseInterface
    {
        $content = $this->contentService->findByUid($content);
        $this->contentService->handleDelete($content);

        $this->addFlashMessage(
            sprintf(
                $this->translate('contentDeletedMessage'),
                $content->getTitle()
            ),
            $this->translate('contentDeleted')
        );

        return new ForwardResponse('index');
    }

    protected function registerDocheaderButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        if ('indexAction' !== $this->actionMethodName) {
            $uri = $this->uriBuilder->uriFor('index');
            $title = $this->translate('back');
            $icon = $this->iconFactory
                ->getIcon('actions-view-go-back', Icon::SIZE_SMALL);
        } else {
            $uri = $this->uriBuilder->reset()->uriFor('new');
            $title = $this->translate('createNewContent');
            $icon = $this->iconFactory
                ->getIcon('actions-document-new', Icon::SIZE_SMALL);
        }

        $button = $buttonBar->makeLinkButton()
            ->setHref($uri)
            ->setTitle($title)
            ->setIcon($icon);
        $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);
    }

    private function showH5pErrorMessages(): void
    {
        foreach ($this->h5pIntegrationService->getH5PCoreInstance()->h5pF->getMessages('error') as $errorMessage) {
            $this->addFlashMessage(
                $errorMessage->message,
                $errorMessage->code ?: $this->translate('h5pError'),
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
            );
        }
    }

    /**
     * Set current selected storage for content creation and display
     */
    protected function setStoragePid(): void
    {
        $storagePid = GeneralUtility::_GET('id');
        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        $persistenceConfiguration = ['persistence' => ['storagePid' => $storagePid]];
        $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));
    }
}
