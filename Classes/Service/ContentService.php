<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Service;

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
use H5peditor;
use H5PStorage;
use LMS3\Lms3h5p\Domain\Model\Content;
use LMS3\Lms3h5p\Domain\Repository\ContentRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Content Service
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
readonly class ContentService
{
    public function __construct(
        private CacheManager $cacheManager,
        private ContentRepository $contentRepository
    ) {}

    /**
     * Creates the content data structure that H5P expects and passes it into its API.
     * If a $contentId is provided, will try to find and update that content. If there
     * is no content with that ID, it will be created.
     */
    public function handleCreateOrUpdate(H5PCore $h5pCore, H5peditor $h5pEditor, string $library, string $parameters, ?int $contentId = null, array $options = []): ?Content
    {
        $content = [];
        $oldLibrary = null;
        $oldParameters = null;

        // Before we save the new data, load the old data from the DB.
        if ($contentId) {
            $content['id'] = $contentId;
            $contentObject = $this->contentRepository->findByUid($content['id']);

            if ($contentObject !== null) {
                $oldLibrary = $contentObject->getLibrary()->toAssocArray();
                $oldParameters = json_decode($contentObject->getParameters());
            }
        }

        $disable = $this->getDisabledContentFeatures($h5pCore, $options);

        $params = json_decode($parameters);
        if ($params === null) {
            $h5pCore->h5pF->setErrorMessage('Invalid parameters.');
            return null;
        }

        // Trim title and check length
        $trimmed_title = empty($params->metadata) ? '' : trim($params->metadata->title);
        if ($trimmed_title === '') {
            $h5pCore->h5pF->setErrorMessage('Missing title.');
            return null;
        }

        $content['disable'] = $disable;
        $content['title'] = $trimmed_title;
        $content['params'] = json_encode($params->params);
        $content['metadata'] = $params->metadata;

        // Get library
        $content['library'] = $h5pCore::libraryFromString($library);
        if (!$content['library']) {
            $h5pCore->h5pF->setErrorMessage('Invalid library.');
            return null;
        }

        // Check if library exists.
        $content['library']['libraryId'] = $h5pCore->h5pF->getLibraryId(
            $content['library']['machineName'],
            $content['library']['majorVersion'],
            $content['library']['minorVersion']
        );
        if (!$content['library']['libraryId']) {
            $h5pCore->h5pF->setErrorMessage('No such library.');
            return null;
        }

        $content['id'] = $h5pCore->saveContent($content);

        // Clear related caches
        $cache = $this->cacheManager->getCache('lms3h5p_libraries');
        $cache->flushByTag('content_' . $content['id']);

        $h5pEditor->processParameters($content['id'], $content['library'], $params->params, $oldLibrary, $oldParameters);
        $contentObject = $this->contentRepository->findByUid($content['id']);

        $content = $contentObject->toAssocArray();
        $content['slug'] = '';
        $h5pCore->filterParameters($content);

        return $contentObject;
    }

    public function handleDelete(H5PCore $h5pCoreInstance, Content $content): void
    {
        $h5pStorage = new H5PStorage($h5pCoreInstance->h5pF, $h5pCoreInstance);
        $h5pStorage->deletePackage($content->toAssocArray());
    }

    /**
     * Find all content records
     *
     * @return QueryResultInterface<int, Content>
     */
    public function findAll(): QueryResultInterface
    {
        return $this->contentRepository->findAll();
    }

    public function findByUid(int $uid): ?Content
    {
        $this->contentRepository->setDefaultQuerySettings(
            $this->contentRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        return $this->contentRepository->findByUid($uid);
    }

    public function findByUids(array $uids): array
    {
        if (empty($uids)) {
            return [];
        }

        $this->contentRepository->setDefaultQuerySettings(
            $this->contentRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        $query = $this->contentRepository->createQuery();
        $where = $query->in('uid', $uids);

        return $query->matching($where)->execute()->toArray();
    }

    protected function getDisabledContentFeatures(H5PCore $core, array $options): int
    {
        $set = [
            H5PCore::DISPLAY_OPTION_FRAME => (bool)$options['frame'],
            H5PCore::DISPLAY_OPTION_DOWNLOAD => (bool)$options['download'],
            H5PCore::DISPLAY_OPTION_EMBED => (bool)$options['embed'],
            H5PCore::DISPLAY_OPTION_COPYRIGHT => (bool)$options['copyright'],
        ];

        return $core->getStorableDisplayOptions($set, H5PCore::DISABLE_NONE);
    }
}
