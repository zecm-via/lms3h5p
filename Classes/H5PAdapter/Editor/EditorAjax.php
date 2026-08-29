<?php

namespace LMS3\Lms3h5p\H5PAdapter\Editor;

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

use H5PEditorAjaxInterface;
use LMS3\Lms3h5p\Domain\Repository\ContentTypeCacheEntryRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryTranslationRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Editor Ajax
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class EditorAjax implements H5PEditorAjaxInterface
{
    protected LibraryRepository $libraryRepository;
    protected LibraryTranslationRepository $libraryTranslationRepository;
    protected ContentTypeCacheEntryRepository $contentTypeCacheEntryRepository;

    public function __construct()
    {
        $this->libraryRepository = GeneralUtility::makeInstance(LibraryRepository::class);
        $this->libraryTranslationRepository = GeneralUtility::makeInstance(LibraryTranslationRepository::class);
        $this->contentTypeCacheEntryRepository = GeneralUtility::makeInstance(
            ContentTypeCacheEntryRepository::class
        );
    }

    /**
     * Gets latest library versions that exists locally
     *
     * @return array Latest version of all local libraries
     */
    public function getLatestLibraryVersions()
    {
        $librariesOrderedByMajorAndMinorVersion = $this->libraryRepository->findLatestLibraryVersions();
        $librariesOrderedByMajorAndMinorVersion = array_map(function ($libraryVersion) {
            return (object)$libraryVersion;
        }, $librariesOrderedByMajorAndMinorVersion);
        return array_values($librariesOrderedByMajorAndMinorVersion);
    }

    /**
     * Get locally stored Content Type Cache. If machine name is provided
     * it will only get the given content type from the cache
     *
     * @param $machineName
     *
     * @return array|object|null Returns results from querying the database
     */
    public function getContentTypeCache($machineName = null)
    {
        if ($machineName !== null) {
            return $this->contentTypeCacheEntryRepository->findOneBy(['machine_name' => $machineName]);
        }

        return $this->contentTypeCacheEntryRepository->getContentTypeCacheObjects();
    }

    /**
     * Gets recently used libraries for the current author
     *
     * @return array machine names. The first element in the array is the
     * most recently used.
     */
    public function getAuthorsRecentlyUsedLibraries(): array
    {
        // TODO: Implement getAuthorsRecentlyUsedLibraries() method.
        return [];
    }

    /**
     * Checks if the provided token is valid for this endpoint
     *
     * @param string $token The token that will be validated for.
     *
     * @return bool True if successful validation
     */
    public function validateEditorToken($token)
    {
        // TODO: Implement validateEditorToken() method.
        return true;
    }

    /**
     * Get translations for a language for a list of libraries
     *
     * @param array $libraries An array of libraries, in the form "<machineName> <majorVersion>.<minorVersion>"
     * @param string $language_code
     * @return array
     */
    public function getTranslations($libraries, $language_code): array
    {
        $libraryTranslations = [];
        foreach ($libraries as $libraryName) {
            preg_match_all('/(.+)\s(\d+)\.(\d+)$/', $libraryName, $matches);
            if ($matches[1] && $matches[2] && $matches[3]) {
                $library = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion(
                    $matches[1][0],
                    (int)$matches[2][0],
                    (int)$matches[3][0]
                );
                $libraryTranslation = $this->libraryTranslationRepository->findOneByLibraryAndLanguage($library, $language_code);
                $libraryTranslations[$libraryName] = $libraryTranslation->getTranslation();
            }
        }
        return $libraryTranslations;
    }
}
