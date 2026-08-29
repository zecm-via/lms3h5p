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

use H5PCore;
use H5peditorStorage;
use LMS3\Lms3h5p\Domain\Model\Library;
use LMS3\Lms3h5p\Domain\Repository\LibraryRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryTranslationRepository;
use LMS3\Lms3h5p\H5PAdapter\TYPO3H5P;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * EditorFileAdapter
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class EditorFileAdapter implements H5peditorStorage
{
    protected LibraryRepository $libraryRepository;
    protected LibraryTranslationRepository $libraryTranslationRepository;

    public function __construct()
    {
        $this->libraryRepository = GeneralUtility::makeInstance(LibraryRepository::class);
        $this->libraryTranslationRepository = GeneralUtility::makeInstance(LibraryTranslationRepository::class);
    }

    /**
     * Load language file(JSON) from database.
     * This is used to translate the editor fields(title, description etc.)
     *
     * @param $machineName
     * @param $majorVersion
     * @param $minorVersion
     * @param $language
     * @return string Translation in JSON format
     */
    public function getLanguage($machineName, $majorVersion, $minorVersion, $language)
    {
        $library = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion(
            $machineName,
            $majorVersion,
            $minorVersion
        );
        $libraryTranslation = $this->libraryTranslationRepository->findOneByLibraryAndLanguage($library, $language);
        if (!$libraryTranslation) {
            return false;
        }

        return $libraryTranslation->getTranslation();
    }

    /**
     * "Callback" for mark the given file as a permanent file.
     * Used when saving content that has new uploaded files.
     *
     * @param int $fileId
     */
    public function keepFile($fileId): void
    {
        // TODO: Implement keepFile() method.
    }

    /**
     * Decides which content types the editor should have.
     *
     * Two usecases:
     * 1. No input, will list all the available content types.
     * 2. Libraries supported are specified, load additional data and verify
     * that the content types are available. Used by e.g. the Presentation Tool
     * Editor that already knows which content types are supported in its
     * slides.
     *
     * @param array $libraries List of library names + version to load info for
     * @return array List of all libraries loaded
     */
    public function getLibraries($libraries = null)
    {
        $librariesWithDetails = [];

        if ($libraries !== null) {
            // Get details for the specified libraries only.
            foreach ($libraries as $libraryData) {
                /** @var Library $library */
                $library = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion(
                    $libraryData->name,
                    $libraryData->majorVersion,
                    $libraryData->minorVersion
                );
                if ($library === null || $library->getSemantics() === null) {
                    continue;
                }
                // Library found, add details to list
                $libraryData->tutorialUrl = $library->getTutorialUrl();
                $libraryData->title = $library->getTitle();
                $libraryData->runnable = $library->isRunnable();
                $libraryData->restricted = false; // for now
                // TODO: Implement the below correctly with auth check
                // $libraryData->restricted = $super_user ? FALSE : $library->isRestricted();
                $librariesWithDetails[] = $libraryData;
            }
            // Done, return list with library details
            return $librariesWithDetails;
        }

        // Load all libraries that have semantics and are runnable
        $libraries = $this->libraryRepository->findByConditions(
            ['runnable' => true],
            ['title' => QueryInterface::ORDER_ASCENDING]
        );
        /** @var Library $library */
        foreach ($libraries as $library) {
            if ($library->getSemantics() === null) {
                continue;
            }
            $libraryData = $library->toStdClass();
            // Make sure we only display the newest version of a library.
            foreach ($librariesWithDetails as $existingLibrary) {
                if ($libraryData->name === $existingLibrary->name) {

                    // Found library with same name, check versions
                    if (($libraryData->majorVersion === $existingLibrary->majorVersion
                            && $libraryData->minorVersion > $existingLibrary->minorVersion)
                        || ($libraryData->majorVersion > $existingLibrary->majorVersion)) {
                        // This is a newer version
                        $existingLibrary->isOld = true;
                    } else {
                        // This is an older version
                        $libraryData->isOld = true;
                    }
                }
            }

            // Check to see if content type should be restricted
            $libraryData->restricted = false; // for now
            // TODO: Implement the below correctly with auth check
            // $libraryData->restricted = $super_user ? FALSE : $library->isRestricted();

            // Add new library
            $librariesWithDetails[] = $libraryData;
        }
        return $librariesWithDetails;
    }

    /**
     * Alter styles and scripts
     *
     * @param array $files
     *  List of files as objects with path and version as properties
     * @param array $libraries
     *  List of libraries indexed by machineName with objects as values. The objects
     *  have majorVersion and minorVersion as properties.
     */
    public function alterLibraryFiles(&$files, $libraries): void
    {
        // TODO: Implement alterLibraryFiles() method.
    }

    /**
     * Saves a file or moves it temporarily. This is often necessary in order to
     * validate and store uploaded or fetched H5Ps.
     *
     * @param string $data Uri of data that should be saved as a temporary file
     * @param bool $move_file Can be set to TRUE to move the data instead of saving it
     *
     * @return object Returns false if saving failed or the path to the file
     *  if saving succeeded
     */
    public static function saveFileTemporarily($data, $move_file)
    {
        $interface = GeneralUtility::makeInstance(TYPO3H5P::class)->getH5PInstance();

        $path = $interface->getUploadedH5pPath();

        if ($move_file) {
            // Move so core can validate the file extension.
            rename($data, $path);
        } else {
            // Create file from data
            file_put_contents($path, $data);
        }

        return (object)[
            'dir' => dirname($path),
            'fileName' => basename($path),
        ];
    }

    /**
     * Marks a file for later cleanup, useful when files are not instantly cleaned
     * up. E.g. for files that are uploaded through the editor.
     *
     * @param $file
     * @param $content_id
     */
    public static function markFileForCleanup($file, $content_id): void
    {
        // TODO: Implement markFileForCleanup() method.
    }

    /**
     * Clean up temporary files
     *
     * @param string $filePath Path to file or directory
     */
    public static function removeTemporarilySavedFiles($filePath): void
    {
        if (is_dir($filePath)) {
            H5PCore::deleteFileTree($filePath);
        } elseif (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Load a list of available language codes from the database.
     *
     * @param string $machineName The machine readable name of the library(content type)
     * @param int $majorVersion Major part of version number
     * @param int $minorVersion Minor part of version number
     * @return array List of possible language codes
     */
    public function getAvailableLanguages($machineName, $majorVersion, $minorVersion)
    {
        $library = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion(
            $machineName,
            $majorVersion,
            $minorVersion
        );
        if ($library === null) {
            return [];
        }

        $languages = [];
        $libraryTranslations = $this->libraryTranslationRepository->findBy(['library' => $library]);
        foreach ($libraryTranslations as $translation) {
            $languages[] = $translation->getLanguageCode();
        }

        return $languages;
    }
}
