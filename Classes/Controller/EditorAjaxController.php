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

use H5PCore;
use H5PEditorEndpoints;
use H5peditorFile;
use LMS3\Lms3h5p\Service\H5PIntegrationService;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Editor Ajax Controller
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class EditorAjaxController extends ActionController
{
    public function __construct(
        private readonly H5PIntegrationService $h5pIntegrationService,
        private readonly CacheManager $cacheManager
    ) {}

    public function indexAction(): void
    {
        $type = $this->request->getQueryParams()['type'];
        switch ($type) {
            case H5PEditorEndpoints::CONTENT_TYPE_CACHE:
                $this->contentTypeCache();
                break;
            case H5PEditorEndpoints::LIBRARY_INSTALL:
                $this->installLibrary();
                break;
            case H5PEditorEndpoints::LIBRARIES:
                $this->libraries();
                break;
            case H5PEditorEndpoints::FILES:
                $this->uploadFiles();
                break;
            case H5PEditorEndpoints::FILTER:
                $this->filter();
                break;
            case H5PEditorEndpoints::LIBRARY_UPLOAD:
                $this->uploadLibrary();
                break;
            case H5PEditorEndpoints::TRANSLATIONS:
                $this->translations();
                break;
            case H5PEditorEndpoints::CONTENT_HUB_METADATA_CACHE:
                $this->contentHubMetadataCache();
                break;
        }

        exit;
    }

    protected function contentTypeCache(): void
    {
        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::CONTENT_TYPE_CACHE
        );
    }

    protected function installLibrary(): void
    {
        $id = $this->request->getQueryParams()['id'];
        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::LIBRARY_INSTALL,
            $this->request->getQueryParams()['moduleToken'],
            $id
        );

        // Clear related caches
        try {
            $this->cacheManager
                ->getCache('lms3h5p_libraries')
                ->flushByTag($this->h5pIntegrationService::getCacheTagForLibrary($id));
        } catch (NoSuchCacheException) {

        }
    }

    protected function libraries(): void
    {
        if ($this->request->hasArgument('libraries')) {
            $this->h5pIntegrationService->getH5pEditor()->ajax->action(
                H5PEditorEndpoints::LIBRARIES
            );
            exit;
        }

        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::SINGLE_LIBRARY,
            $this->request->getQueryParams()['machineName'],
            $this->request->getQueryParams()['majorVersion'],
            $this->request->getQueryParams()['minorVersion'],
            $this->getBELanguage(),
            '',
            Environment::getPublicPath() . $this->h5pIntegrationService->getSettings()['h5pPublicFolder']['path'],
            'en'
        );
    }

    protected function uploadFiles(): void
    {
        $h5pCore = $this->h5pIntegrationService->getH5PCoreInstance();
        $file = new H5peditorFile($h5pCore->h5pF);
        if (!$file->isLoaded()) {
            H5PCore::ajaxError($h5pCore->h5pF->t('File not found on server. Check file upload settings.'));
            return;
        }
        // Make sure file is valid and mark it for cleanup at a later time
        if ($file->validate()) {
            $file_id = $h5pCore->fs->saveFile($file, 0);
            $this->h5pIntegrationService->getH5pEditor()->ajax->storage->markFileForCleanup($file_id, 0);
        }
        $file->printResult();
    }

    protected function filter(): void
    {
        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::FILTER,
            $this->request->getQueryParams()['token'],
            $this->request->getParsedBody()['libraryParameters'],
        );
    }

    protected function uploadLibrary(): void
    {
        $libraryId = (int)($this->request->getQueryParams()['id'] ?? 0);

        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::LIBRARY_UPLOAD,
            $this->request->getQueryParams()['moduleToken'],
            $_FILES['h5p']['tmp_name'],
            $libraryId
        );

        // Clear related caches
        try {
            $this->cacheManager
                ->getCache('lms3h5p_libraries')
                ->flushByTag($this->h5pIntegrationService::getCacheTagForLibrary((string)$libraryId));
        } catch (NoSuchCacheException) {
        }
    }

    protected function translations(): void
    {
        $language = $this->request->getQueryParams()['language'];

        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::TRANSLATIONS,
            $language
        );
    }

    protected function contentHubMetadataCache(): void
    {
        $this->h5pIntegrationService->getH5pEditor()->ajax->action(
            H5PEditorEndpoints::CONTENT_HUB_METADATA_CACHE,
            $this->getBELanguage()
        );
    }

    /**
     * @return string
     */
    public function getBELanguage(): string
    {
        $language = $GLOBALS['BE_USER']->user['lang'];
        if (empty($language) || $language === 'default') {
            $language = 'en';
        }
        return $language;
    }
}
