<?php

namespace LMS3\Lms3h5p\H5PAdapter\Core;

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

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use H5PCore;
use H5PFrameworkInterface;
use LMS3\Lms3h5p\Domain\Model\Content;
use LMS3\Lms3h5p\Domain\Model\ContentDependency;
use LMS3\Lms3h5p\Domain\Model\ContentTypeCacheEntry;
use LMS3\Lms3h5p\Domain\Model\Library;
use LMS3\Lms3h5p\Domain\Model\LibraryDependency;
use LMS3\Lms3h5p\Domain\Model\LibraryTranslation;
use LMS3\Lms3h5p\Domain\Model\Setting;
use LMS3\Lms3h5p\Domain\Repository\CachedAssetRepository;
use LMS3\Lms3h5p\Domain\Repository\ContentDependencyRepository;
use LMS3\Lms3h5p\Domain\Repository\ContentRepository;
use LMS3\Lms3h5p\Domain\Repository\ContentTypeCacheEntryRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryDependencyRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryTranslationRepository;
use LMS3\Lms3h5p\Domain\Repository\SettingRepository;
use LMS3\Lms3h5p\H5PAdapter\TYPO3H5P;
use stdClass;
use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * H5P Framework
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class H5PFramework implements H5PFrameworkInterface, SingletonInterface
{
    public const string PLATFORM_NAME = 'TYPO3 CMS';

    protected PackageManager $packageManager;
    protected SettingRepository $settingRepository;
    protected ContentTypeCacheEntryRepository $contentTypeCacheEntryRepository;
    protected LibraryRepository $libraryRepository;
    protected PersistenceManagerInterface $persistenceManager;
    protected LibraryTranslationRepository $libraryTranslationRepository;
    protected LibraryDependencyRepository $libraryDependencyRepository;
    protected ContentRepository $contentRepository;
    protected ContentDependencyRepository $contentDependencyRepository;
    protected CachedAssetRepository $cachedAssetRepository;
    protected ConfigurationManagerInterface $configurationManager;

    protected array $messages;

    public function __construct()
    {
        $this->settingRepository = GeneralUtility::makeInstance(SettingRepository::class);
        $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $this->contentTypeCacheEntryRepository = GeneralUtility::makeInstance(ContentTypeCacheEntryRepository::class);
        $this->libraryRepository = GeneralUtility::makeInstance(LibraryRepository::class);
        $this->libraryDependencyRepository = GeneralUtility::makeInstance(LibraryDependencyRepository::class);
        $this->libraryTranslationRepository = GeneralUtility::makeInstance(LibraryTranslationRepository::class);
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->contentRepository = GeneralUtility::makeInstance(ContentRepository::class);
        $this->contentDependencyRepository = GeneralUtility::makeInstance(ContentDependencyRepository::class);
        $this->cachedAssetRepository = GeneralUtility::makeInstance(CachedAssetRepository::class);
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);

        $this->setDefaultStorage();
    }

    /**
     * Set default storage
     */
    protected function setDefaultStorage(): void
    {
        $this->settingRepository->setDefaultQuerySettings(
            $this->settingRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $this->contentTypeCacheEntryRepository->setDefaultQuerySettings(
            $this->contentTypeCacheEntryRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $this->contentDependencyRepository->setDefaultQuerySettings(
            $this->contentDependencyRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $this->libraryRepository->setDefaultQuerySettings(
            $this->libraryRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $this->libraryDependencyRepository->setDefaultQuerySettings(
            $this->libraryDependencyRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $this->libraryTranslationRepository->setDefaultQuerySettings(
            $this->libraryTranslationRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $this->cachedAssetRepository->setDefaultQuerySettings(
            $this->cachedAssetRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
    }

    protected function getInjectedH5PCore(): H5PCore
    {
        return GeneralUtility::makeInstance(TYPO3H5P::class)->getH5PInstance('core');
    }

    /**
     * Returns info for the current platform
     *
     * @return array
     *   An associative array containing:
     *   - name: The name of the platform, for instance "Wordpress"
     *   - version: The version of the platform, for instance "4.0"
     *   - h5pVersion: The version of the H5P plugin/module
     * @throws UnknownPackageException
     */
    public function getPlatformInfo(): array
    {
        return [
            'name' => self::PLATFORM_NAME,
            'version' => $this->packageManager->getPackage('core')->getPackageMetaData()->getVersion(),
            'h5pVersion' => $this->getOption('h5p_version'),
        ];
    }

    /**
     * Fetches a file from a remote server using HTTP GET
     *
     * @param  string  $url  Where you want to get or send data.
     * @param  array  $data  Data to post to the URL.
     * @param  bool  $blocking  Set to 'FALSE' to instantly time out (fire and forget).
     * @param  string  $stream  Path to where the file should be saved.
     * @param  bool  $fullData  Return additional response data such as headers and potentially other data
     * @param  array  $headers  Headers to send
     * @param  array  $files Files to send
     * @param  string  $method
     *
     * @return string|array The content (response body), or an array with data. NULL if something went wrong
     */
    public function fetchExternalData($url, $data = null, $blocking = true, $stream = null, $fullData = false, $headers = [], $files = [], $method = 'POST')
    {
        $client = new Client();
        $options = [
            'synchronous' => $blocking,
            'sink' => $stream,
            'form_params' => $data,
        ];

        try {
            $response = $client->request($data === null ? 'GET' : 'POST', $url, $options);
            if ($fullData) {
                return [
                    'status' => $response->getStatusCode(),
                    'data' => $response->getBody()->getContents(),
                ];
            }
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return $response->getBody()->getSize() ? $response->getBody()->getContents() : '';
            }
        } catch (GuzzleException $e) {
            $this->setErrorMessage($e->getMessage(), 'failed-fetching-external-data');
        }

        return null;
    }

    /**
     * Set the tutorial URL for a library. All versions of the library is set
     *
     * @param string $machineName
     * @param string $tutorialUrl
     */
    public function setLibraryTutorialUrl($machineName, $tutorialUrl): void
    {
        // TODO: Implement setLibraryTutorialUrl() method.
    }

    /**
     * Show the user an error message
     *
     * @param string $message The error message
     * @param string $code An optional code
     */
    public function setErrorMessage($message, $code = null): void
    {
        $this->messages['error'][] = (object)[
            'code' => $code,
            'message' => $message,
        ];
    }

    /**
     * Show the user an information message
     *
     * @param string $message
     *  The error message
     */
    public function setInfoMessage($message): void
    {
        $this->messages['info'][] = $message;
    }

    /**
     * Return messages
     *
     * @param string $type 'info' or 'error'
     * @return string[]
     */
    public function getMessages($type): array
    {
        if (empty($this->messages[$type])) {
            return [];
        }
        $messages = $this->messages[$type];
        $this->messages[$type] = [];

        return $messages;
    }

    /**
     * Translation function
     *
     * @param string $message
     *  The english string to be translated.
     * @param array $replacements
     *   An associative array of replacements to make after translation. Incidences
     *   of any key in this array are replaced with the corresponding value. Based
     *   on the first character of the key, the value is escaped and/or themed
     * @return string Translated string
     * Translated string
     */
    public function t($message, $replacements = []): string
    {
        // Insert !var as is, escape @var and emphasis %var.
        foreach ($replacements as $key => $replacement) {
            if ($key[0] === '@') {
                $replacements[$key] = htmlspecialchars((string)$replacement);
            } elseif ($key[0] === '%') {
                $replacements[$key] = '<em>' . htmlspecialchars((string)$replacement) . '</em>';
            }
        }
        $message = preg_replace('/([!@%])[a-z0-9-]+/i', '%s', $message);

        return vsprintf($message, $replacements);
    }

    /**
     * Get URL to file in the specific library
     * @param string $libraryFolderName
     * @param string $fileName
     * @return string URL to file
     */
    public function getLibraryFileUrl($libraryFolderName, $fileName)
    {
        return sprintf(
            '%s/libraries/%s/%s',
            rtrim($this->getInjectedH5PCore()->url, '/'),
            $libraryFolderName,
            $fileName
        );
    }

    /**
     * Get the Path to the last uploaded h5p
     *
     * @return string
     *   Path to the folder where the last uploaded h5p for this session is located.
     */
    public function getUploadedH5pFolderPath()
    {
        static $dir;
        if (is_null($dir)) {
            $dir = $this->getInjectedH5PCore()->fs->getTmpPath();
        }

        return $dir;
    }

    /**
     * Get the path to the last uploaded h5p file
     *
     * @return string
     *   Path to the last uploaded h5p
     */
    public function getUploadedH5pPath()
    {
        static $path;
        if (is_null($path)) {
            $path = $this->getInjectedH5PCore()->fs->getTmpPath() . '.h5p';
        }

        return $path;
    }

    /**
     * Get a list of the current installed libraries
     *
     * @return array
     *   Associative array containing one entry per machine name.
     *   For each machineName there is a list of libraries(with different versions)
     */
    public function loadLibraries()
    {
        $installedLibraries = $this->libraryRepository->findAll();

        $versionsArray = [];
        foreach ($installedLibraries as $library) {
            /** @var Library $library */
            $versionsArray[$library->getName()][] = $library->toStdClass();
        }

        return $versionsArray;
    }

    /**
     * Returns the URL to the library admin page
     *
     * @return string
     *   URL to admin page
     */
    public function getAdminUrl(): string
    {
        // TODO: Implement getAdminUrl() method.
        return '';
    }

    /**
     * Get id to an existing library.
     * If version number is not specified, the newest version will be returned.
     *
     * @param string $machineName
     *   The librarys machine name
     * @param int $majorVersion
     *   Optional major version number for library
     * @param int $minorVersion
     *   Optional minor version number for library
     * @return int|false
     *   The id of the specified library or FALSE
     */
    public function getLibraryId($machineName, $majorVersion = null, $minorVersion = null)
    {
        $criteria = ['name' => $machineName];
        if ($majorVersion !== null) {
            $criteria['majorVersion'] = $majorVersion;
        }
        if ($minorVersion !== null) {
            $criteria['minorVersion'] = $minorVersion;
        }

        $libraries = $this->libraryRepository->findByConditions(
            $criteria,
            [
                'majorVersion' => QueryInterface::ORDER_DESCENDING,
                'minorVersion' => QueryInterface::ORDER_DESCENDING,
                'patchVersion' => QueryInterface::ORDER_DESCENDING,
            ]
        );

        if (count($libraries) > 0) {
            $library = $libraries[0];
            return $library->getUid();
        }
        return false;
    }

    /**
     * Get file extension whitelist
     *
     * The default extension list is part of h5p, but admins should be allowed to modify it
     *
     * @param bool $isLibrary
     *   TRUE if this is the whitelist for a library. FALSE if it is the whitelist
     *   for the content folder we are getting
     * @param string $defaultContentWhitelist
     *   A string of file extensions separated by whitespace
     * @param string $defaultLibraryWhitelist
     *   A string of file extensions separated by whitespace
     * @return string
     */
    public function getWhitelist($isLibrary, $defaultContentWhitelist, $defaultLibraryWhitelist)
    {
        $whitelist = $defaultContentWhitelist;
        if ($isLibrary) {
            $whitelist .= ' ' . $defaultLibraryWhitelist;
        }
        return $whitelist;
    }

    /**
     * Is the library a patched version of an existing library?
     *
     * @param object $library
     *   An associative array containing:
     *   - machineName: The library machineName
     *   - majorVersion: The librarys majorVersion
     *   - minorVersion: The librarys minorVersion
     *   - patchVersion: The librarys patchVersion
     * @return bool
     *   TRUE if the library is a patched version of an existing library
     *   FALSE otherwise
     */
    public function isPatchedLibrary($library)
    {
        $criteria = [
            'name' => $library['machineName'],
            'majorVersion' => $library['majorVersion'],
            'minorVersion' => $library['minorVersion'],
            'patchVersion' => $library['patchVersion'],
        ];

        return $this->libraryRepository->isPatchedLibrary($criteria);
    }

    /**
     * Is H5P in development mode?
     *
     * @return bool
     *  TRUE if H5P development mode is active
     *  FALSE otherwise
     */
    public function isInDevMode(): bool
    {
        // TODO: Implement isInDevMode() method.
        return false;
    }

    /**
     * Is the current user allowed to update libraries?
     *
     * @return bool
     *  TRUE if the user is allowed to update libraries
     *  FALSE if the user is not allowed to update libraries
     */
    public function mayUpdateLibraries()
    {
        // TODO: Proper implementation
        return true;
    }

    /**
     * Store data about a library
     *
     * Also fills in the libraryId in the libraryData object if the object is new
     *
     * @param array $libraryData
     *   Associative array containing:
     *   - libraryId: The id of the library if it is an existing library.
     *   - title: The library's name
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     *   - patchVersion: The library's patchVersion
     *   - runnable: 1 if the library is a content type, 0 otherwise
     *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
     *   - embedTypes(optional): list of supported embed types
     *   - preloadedJs(optional): list of associative arrays containing:
     *     - path: path to a js file relative to the library root folder
     *   - preloadedCss(optional): list of associative arrays containing:
     *     - path: path to css file relative to the library root folder
     *   - dropLibraryCss(optional): list of associative arrays containing:
     *     - machineName: machine name for the librarys that are to drop their css
     *   - semantics(optional): Json describing the content structure for the library
     *   - language(optional): associative array containing:
     *     - languageCode: Translation in json format
     * @param bool $new
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function saveLibraryData(&$libraryData, $new = true): void
    {
        if ($new) {
            $library = Library::createFromMetadata($libraryData);
            $this->libraryRepository->add($library);
            // Persist and re-read the entity to generate the library ID in the DB and fill the field
            $this->persistenceManager->persistAll();
            $this->persistenceManager->clearState();
            $libraryData['libraryId'] = $library->getUid();
        } else {
            /** @var Library $library */
            $library = $this->libraryRepository->findByUid($libraryData['libraryId']);
            if ($library === null) {
                throw new Exception('Library with ID ' . $libraryData['libraryId'] . ' could not be found!', 8225304040);
            }
            $library->updateFromMetadata($libraryData);
            $this->libraryRepository->update($library);
            $this->deleteLibraryDependencies($libraryData['libraryId']);
        }

        // Update languages
        $translations = $this->libraryTranslationRepository->findBy(['library' => $library]);
        /** @var LibraryTranslation $translation */
        foreach ($translations as $translation) {
            $this->libraryTranslationRepository->remove($translation);
        }

        // Persist before we create new translations
        $this->persistenceManager->persistAll();

        if (isset($libraryData['language'])) {
            foreach ($libraryData['language'] as $languageCode => $translation) {
                $libraryTranslation = LibraryTranslation::create($library, $languageCode, $translation);
                $this->libraryTranslationRepository->add($libraryTranslation);
            }
            $this->persistenceManager->persistAll();
        }
    }

    /**
     * Insert new content.
     *
     * @param array $content
     *   An associative array containing:
     *   - id: The content id
     *   - params: The content in json format
     *   - library: An associative array containing:
     *     - libraryId: The id of the main library for this content
     * @param int $contentMainId
     *   Main id for the content if this is a system that supports versions
     * @return int
     * @throws IllegalObjectTypeException
     */
    public function insertContent($content, $contentMainId = null)
    {
        /** @var Library $library */
        $library = $this->libraryRepository->findByUid($content['library']['libraryId']);
        $account = $GLOBALS['BE_USER']->user['uid'];
        $contentObject = Content::createFromMetadata($content, $library, $account);

        // Persist and re-read the entity to generate the content ID in the DB and fill the field
        $this->contentRepository->add($contentObject);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        return $contentObject->getUid();
    }

    /**
     * Update old content.
     *
     * @param array $content
     *   An associative array containing:
     *   - id: The content id
     *   - params: The content in json format
     *   - library: An associative array containing:
     *     - libraryId: The id of the main library for this content
     * @param int $contentMainId
     *   Main id for the content if this is a system that supports versions
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function updateContent($content, $contentMainId = null): void
    {
        $contentObject = $this->contentRepository->findByUid($content['id']);
        if ($contentObject === null) {
            return;
        }

        $library = $this->libraryRepository->findByUid($content['library']['libraryId']);
        if ($library === null) {
            return;
        }

        $contentObject->updateFromMetadata($content, $library);

        $this->contentRepository->update($contentObject);
    }

    /**
     * Resets marked user data for the given content.
     *
     * @param int $contentId
     */
    public function resetContentUserData($contentId): void
    {
        // TODO: Implement resetContentUserData() method.
    }

    /**
     * Save what libraries a library is depending on
     *
     * @param int $libraryId
     *   Library Id for the library we're saving dependencies for
     * @param array $dependencies
     *   List of dependencies as associative arrays containing:
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     * @param string $dependency_type
     *   What type of dependency this is, the following values are allowed:
     *   - editor
     *   - preloaded
     *   - dynamic
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function saveLibraryDependencies($libraryId, $dependencies, $dependency_type): void
    {
        $dependingLibrary = $this->libraryRepository->findByUid($libraryId);
        if ($dependingLibrary === null) {
            throw new Exception('The Library with ID ' . $libraryId . ' could not be found.', 8901499604);
        }

        foreach ($dependencies as $dependency) {
            // Load the library we're depending on
            /** @var Library $requiredLibrary */
            $requiredLibrary = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion(
                $dependency['machineName'],
                $dependency['majorVersion'],
                $dependency['minorVersion']
            );
            // We don't have this library and thus can't register a dependency
            if ($requiredLibrary === null) {
                continue;
            }
            /** @var LibraryDependency $existingDependency */
            $query = $this->libraryDependencyRepository->createQuery();
            $query->matching($query->logicalAnd(
                $query->equals('uidLocal', $dependingLibrary->getUid()),
                $query->equals('uidForeign', $requiredLibrary->getUid())
            ));
            $existingDependency = $query->execute()->getFirst();
            if ($existingDependency !== null) {
                // Dependency exists, only update the type
                $existingDependency->setDependencyType($dependency_type);
                $this->libraryDependencyRepository->update($existingDependency);
            } else {
                // Depedency does not exist, create it
                $dependency = GeneralUtility::makeInstance(LibraryDependency::class);
                $dependency
                    ->setDependencyType($dependency_type)
                    ->setLibrary($dependingLibrary)
                    ->setRequiredLibrary($requiredLibrary);
                $this->libraryDependencyRepository->add($dependency);
                $this->persistenceManager->persistAll();
            }
        }
    }

    /**
     * Give an H5P the same library dependencies as a given H5P
     *
     * @param int $contentId
     *   Id identifying the content
     * @param int $copyFromId
     *   Id identifying the content to be copied
     * @param int $contentMainId
     *   Main id for the content, typically used in frameworks
     *   That supports versions. (In this case the content id will typically be
     *   the version id, and the contentMainId will be the frameworks content id
     */
    public function copyLibraryUsage($contentId, $copyFromId, $contentMainId = null): void
    {
        // TODO: Implement copyLibraryUsage() method.
    }

    /**
     * Deletes content data
     *
     * @param int $contentId
     *   Id identifying the content
     * @throws IllegalObjectTypeException
     */
    public function deleteContentData($contentId): void
    {
        $content = $this->contentRepository->findByUid($contentId);
        if ($content === null) {
            return;
        }
        $this->deleteLibraryUsage($contentId);
        $this->contentRepository->remove($content);
    }

    /**
     * Delete what libraries a content item is using
     *
     * @param int $contentId
     *   Content Id of the content we'll be deleting library usage for
     * @throws IllegalObjectTypeException
     */
    public function deleteLibraryUsage($contentId): void
    {
        $content = $this->contentRepository->findByUid($contentId);
        if ($content === null) {
            return;
        }
        $contentDependencies = $this->contentDependencyRepository->findBy(['content' => $content->getUid()]);
        foreach ($contentDependencies as $contentDependency) {
            $this->contentDependencyRepository->remove($contentDependency);
        }
        // Persist, because directly afterwards saveLibraryUsage() might be called
        $this->persistenceManager->persistAll();
    }

    /**
     * Saves what libraries the content uses
     *
     * @param int $contentId
     *   Id identifying the content
     * @param array $librariesInUse
     *   List of libraries the content uses. Libraries consist of associative arrays with:
     *   - library: Associative array containing:
     *     - dropLibraryCss(optional): comma separated list of machineNames
     *     - machineName: Machine name for the library
     *     - libraryId: Id of the library
     *   - type: The dependency type. Allowed values:
     *     - editor
     *     - dynamic
     *     - preloaded
     * @throws IllegalObjectTypeException
     */
    public function saveLibraryUsage($contentId, $librariesInUse): void
    {
        /** @var Content $content */
        $content = $this->contentRepository->findByUid($contentId);
        if ($content === null) {
            return;
        }

        $dropLibraryCssList = [];
        foreach ($librariesInUse as $dependencyData) {
            if (!empty($dependencyData['library']['dropLibraryCss'])) {
                $dropLibraryCssList = array_merge($dropLibraryCssList, explode(', ', (string)$dependencyData['library']['dropLibraryCss']));
            }
        }

        foreach ($librariesInUse as $dependencyData) {
            $contentDependency = GeneralUtility::makeInstance(ContentDependency::class);
            $contentDependency->setContent($content);
            $contentDependency->setLibrary($this->libraryRepository->findByUid($dependencyData['library']['libraryId']));
            $contentDependency->setDependencyType($dependencyData['type']);
            $contentDependency->setDropCss(in_array($dependencyData['library']['machineName'], $dropLibraryCssList, true));
            $contentDependency->setWeight($dependencyData['weight']);
            $this->contentDependencyRepository->add($contentDependency);
        }
        $this->persistenceManager->persistAll();
    }

    /**
     * Get number of content/nodes using a library, and the number of
     * dependencies to other libraries
     *
     * @param int $libraryId
     *   Library identifier
     * @param bool $skipContent
     *   Flag to indicate if content usage should be skipped
     * @return array
     *   Associative array containing:
     *   - content: Number of content using the library
     *   - libraries: Number of libraries depending on the library
     */
    public function getLibraryUsage($libraryId, $skipContent = false): array
    {
        // TODO: Implement getLibraryUsage() method.
        return [
            'content' => 0,
            'libraries' => 0,
        ];
    }

    /**
     * Loads a library
     *
     * @param string $machineName
     *   The library's machine name
     * @param int $majorVersion
     *   The library's major version
     * @param int $minorVersion
     *   The library's minor version
     * @return array|false
     *   FALSE if the library does not exist.
     *   Otherwise an associative array containing:
     *   - libraryId: The id of the library if it is an existing library.
     *   - title: The library's name
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     *   - patchVersion: The library's patchVersion
     *   - runnable: 1 if the library is a content type, 0 otherwise
     *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
     *   - embedTypes(optional): list of supported embed types
     *   - preloadedJs(optional): comma separated string with js file paths
     *   - preloadedCss(optional): comma separated sting with css file paths
     *   - dropLibraryCss(optional): list of associative arrays containing:
     *     - machineName: machine name for the librarys that are to drop their css
     *   - semantics(optional): Json describing the content structure for the library
     *   - preloadedDependencies(optional): list of associative arrays containing:
     *     - machineName: Machine name for a library this library is depending on
     *     - majorVersion: Major version for a library this library is depending on
     *     - minorVersion: Minor for a library this library is depending on
     *   - dynamicDependencies(optional): list of associative arrays containing:
     *     - machineName: Machine name for a library this library is depending on
     *     - majorVersion: Major version for a library this library is depending on
     *     - minorVersion: Minor for a library this library is depending on
     *   - editorDependencies(optional): list of associative arrays containing:
     *     - machineName: Machine name for a library this library is depending on
     *     - majorVersion: Major version for a library this library is depending on
     *     - minorVersion: Minor for a library this library is depending on
     */
    public function loadLibrary($machineName, $majorVersion, $minorVersion)
    {
        /** @var Library $library */
        $library = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion($machineName, $majorVersion, $minorVersion);
        if ($library === null) {
            return false;
        }

        return $library->toAssocArray();
    }

    /**
     * Loads library semantics.
     *
     * @param string $machineName
     *   Machine name for the library
     * @param int $majorVersion
     *   The library's major version
     * @param int $minorVersion
     *   The library's minor version
     * @return string
     *   The library's semantics as json
     */
    public function loadLibrarySemantics($machineName, $majorVersion, $minorVersion): string
    {
        $library = $this->libraryRepository->findOneByNameMajorVersionAndMinorVersion($machineName, $majorVersion, $minorVersion);
        return (string)$library?->getSemantics();
    }

    /**
     * Makes it possible to alter the semantics, adding custom fields, etc.
     *
     * @param array $semantics
     *   Associative array representing the semantics
     * @param string $machineName
     *   The library's machine name
     * @param int $majorVersion
     *   The library's major version
     * @param int $minorVersion
     *   The library's minor version
     */
    public function alterLibrarySemantics(&$semantics, $machineName, $majorVersion, $minorVersion): void
    {
        // TODO: Implement alterLibrarySemantics() method.
    }

    /**
     * Delete all dependencies belonging to given library
     *
     * @param int $libraryId
     *   Library identifier
     * @throws IllegalObjectTypeException
     */
    public function deleteLibraryDependencies($libraryId): void
    {
        $library = $this->libraryRepository->findByUid($libraryId);
        if ($library === null) {
            return;
        }
        $dependencies = $this->libraryDependencyRepository->findBy(['uid_local' => $library]);
        foreach ($dependencies as $dependency) {
            $this->libraryDependencyRepository->remove($dependency);
        }

        // Make sure we persist here, because new dependencies can be created right afterwards
        $this->persistenceManager->persistAll();
    }

    /**
     * Start an atomic operation against the dependency storage
     */
    public function lockDependencyStorage(): void
    {
        // TODO: Implement lockDependencyStorage() method.
    }

    /**
     * Stops an atomic operation against the dependency storage
     */
    public function unlockDependencyStorage(): void
    {
        // TODO: Implement unlockDependencyStorage() method.
    }

    /**
     * Delete a library from database and file system
     *
     * @param stdClass $library
     *   Library object with id, name, major version and minor version.
     * @throws IllegalObjectTypeException
     */
    public function deleteLibrary($library): void
    {
        $this->deleteLibraryDependencies($library->id);
        $this->libraryRepository->removeById($library->id);
    }

    /**
     * Load content.
     *
     * @param int $id
     *   Content identifier
     * @return array
     *   Associative array containing:
     *   - contentId: Identifier for the content
     *   - params: json content as string
     *   - embedType: csv of embed types
     *   - title: The contents title
     *   - language: Language code for the content
     *   - libraryId: Id for the main library
     *   - libraryName: The library machine name
     *   - libraryMajorVersion: The library's majorVersion
     *   - libraryMinorVersion: The library's minorVersion
     *   - libraryEmbedTypes: CSV of the main library's embed types
     *   - libraryFullscreen: 1 if fullscreen is supported. 0 otherwise.
     */
    public function loadContent($id): array
    {
        // TODO: Implement loadContent() method.
        return [];
    }

    /**
     * Load dependencies for the given content of the given type.
     *
     * @param int $id
     *   Content identifier
     * @param string|null $type
     *   Dependency types. Allowed values:
     *   - editor
     *   - preloaded
     *   - dynamic
     * @return array
     *   List of associative arrays containing:
     *   - libraryId: The id of the library if it is an existing library.
     *   - machineName: The library machineName
     *   - majorVersion: The library's majorVersion
     *   - minorVersion: The library's minorVersion
     *   - patchVersion: The library's patchVersion
     *   - preloadedJs(optional): comma separated string with js file paths
     *   - preloadedCss(optional): comma separated sting with css file paths
     *   - dropCss(optional): csv of machine names
     */
    public function loadContentDependencies($id, $type = null)
    {
        $dependencyArray = [];
        /** @var Content $content */
        $content = $this->contentRepository->findByUid($id);
        if ($content === null) {
            return $dependencyArray;
        }

        $criteria = [
            'content' => $content,
        ];
        if ($type !== null) {
            $criteria['dependencyType'] = $type;
        }

        $dependencies = $this->contentDependencyRepository->findBy($criteria, ['weight' => QueryInterface::ORDER_ASCENDING]);
        foreach ($dependencies as $dependency) {
            $dependencyArray[] = $dependency->toAssocArray();
        }

        return $dependencyArray;
    }

    /**
     * Get stored setting.
     *
     * @param string $name
     *   Identifier for the setting
     * @param string $default
     *   Optional default value if settings is not set
     * @return mixed
     *   Whatever has been stored as the setting
     */
    public function getOption($name, $default = null)
    {
        $configSetting = $this->settingRepository->findOneBy(['config_key' => $name]);

        if ($configSetting !== null) {
            return $configSetting->getConfigValue();
        }

        // Check if there is a default value in the existing config
        return $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)['config'][$name] ?? $default;
    }

    /**
     * Stores the given setting.
     * For example when did we last check h5p.org for updates to our libraries.
     *
     * @param string $name
     *   Identifier for the setting
     * @param mixed $value Data
     *   Whatever we want to store as the setting
     * @throws UnknownObjectException
     */
    public function setOption($name, $value): void
    {
        $configSetting = $this->settingRepository->findOneBy(['config_key' => $name]);
        try {
            if ($configSetting !== null) {
                $configSetting->setConfigValue($value);
                $this->settingRepository->update($configSetting);
            } else {
                $configSetting = GeneralUtility::makeInstance(Setting::class);
                $configSetting->setConfigKey($name);
                $configSetting->setConfigValue($value);
                $this->settingRepository->add($configSetting);
            }
            $this->persistenceManager->persistAll();
        } catch (IllegalObjectTypeException) {
            // Swallow, will never happen
        }
    }

    /**
     * This will update selected fields on the given content.
     *
     * @param int $id Content identifier
     * @param array $fields Content fields, e.g. filtered or slug.
     * @throws UnknownObjectException
     */
    public function updateContentFields($id, $fields): void
    {
        /** @var Content $content */
        $content = $this->contentRepository->findByUid($id);
        if ($content === null) {
            return;
        }

        foreach ($fields as $propertyName => $value) {
            ObjectAccess::setProperty($content, $propertyName, $value);
        }

        try {
            $this->contentRepository->update($content);
        } catch (IllegalObjectTypeException) {
            // will never happen
        }
    }

    /**
     * Will clear filtered params for all the content that uses the specified
     * library. This means that the content dependencies will have to be rebuilt,
     * and the parameters re-filtered.
     *
     * @param array $library_ids
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function clearFilteredParameters($library_ids): void
    {
        foreach ((array)$library_ids as $id) {
            $library = $this->libraryRepository->findByUid($id);
            if ($library === null) {
                throw new Exception('Library with ID ' . $id . ' could not be found!', 7183067522);
            }
            $contentsOfThisLibrary = $this->contentRepository->findBy(['library' => $library]);
            foreach ($contentsOfThisLibrary as $content) {
                $content->setFiltered('');
                $this->contentRepository->update($content);
            }
        }
    }

    /**
     * Get number of contents that has to get their content dependencies rebuilt
     * and parameters re-filtered.
     *
     * @return int
     */
    public function getNumNotFiltered(): int
    {
        // TODO: Implement getNumNotFiltered() method.
        return 0;
    }

    /**
     * Get number of contents using library as main library.
     *
     * @param int $libraryId
     * @param array $skip
     * @return int
     */
    public function getNumContent($libraryId, $skip = null)
    {
        $library = $this->libraryRepository->findByUid($libraryId);
        if ($skip === null) {
            return $this->contentRepository->count(['library' => $library]);
        }

        return $this->contentRepository->countByLibraryAndSkipped($library, $skip);
    }

    /**
     * Determines if content slug is used.
     *
     * @param string $slug
     * @return bool
     */
    public function isContentSlugAvailable($slug)
    {
        return $this->contentRepository->findOneBy(['slug' => $slug]) === null;
    }

    /**
     * Generates statistics from the event log per library
     *
     * @param string $type Type of event to generate stats for
     * @return array Number values indexed by library name and version
     */
    public function getLibraryStats($type)
    {
        // TODO: Implement getLibraryStats() method.
        return [];
    }

    /**
     * Aggregate the current number of H5P authors
     * @return int
     */
    public function getNumAuthors()
    {
        // TODO: Implement getNumAuthors() method.
        return 0;
    }

    /**
     * Stores hash keys for cached assets, aggregated JavaScripts and
     * stylesheets, and connects it to libraries so that we know which cache file
     * to delete when a library is updated.
     *
     * @param string $key
     *  Hash key for the given libraries
     * @param array $libraries
     *  List of dependencies(libraries) used to create the key
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function saveCachedAssets($key, $libraries): void
    {
        /**
         * This is called after FileAdapter->cacheAssets and makes the assignment of
         * CachedAsset and Library.
         * @see FileAdapter::cacheAssets()
         * @see H5PCore::getDependenciesFiles()
         */
        $cachedAssets = $this->cachedAssetRepository->findBy(['hash_key' => $key]);

        foreach ($cachedAssets as $cachedAsset) {
            foreach ($libraries as $libraryData) {
                /** @var Library $library */
                $library = $this->libraryRepository->findByUid($libraryData['libraryId']);
                if ($library === null) {
                    continue;
                }
                $cachedAsset->setLibrary($library);
                $this->cachedAssetRepository->update($cachedAsset);
            }
        }
    }

    /**
     * Locate hash keys for given library and delete them.
     * Used when cache file are deleted.
     *
     * @param int $library_id
     *  Library identifier
     * @return array
     *  List of hash keys removed
     * @throws IllegalObjectTypeException
     */
    public function deleteCachedAssets($library_id)
    {
        $removedKeys = [];

        /** @var Library $library */
        $library = $this->libraryRepository->findByUid($library_id);
        if ($library === null) {
            return $removedKeys;
        }

        $cachedAssetsForLibrary = $this->cachedAssetRepository->findBy(['library' => $library]);
        foreach ($cachedAssetsForLibrary as $cachedAsset) {
            $removedKeys[] = $this->persistenceManager->getIdentifierByObject($cachedAsset);
            $this->cachedAssetRepository->remove($cachedAsset);
        }

        return $removedKeys;
    }

    /**
     * Get the amount of content items associated to a library
     * return int
     */
    public function getLibraryContentCount(): void
    {
        // TODO: Implement getLibraryContentCount() method.
    }

    /**
     * Will trigger after the export file is created.
     */
    public function afterExportCreated($content, $filename): void
    {
        // TODO: Implement afterExportCreated() method.
    }

    /**
     * Check if user has permissions to an action
     *
     * @param mixed $permission Permission type, ref H5PPermission
     * @param mixed $id Id need by platform to determine permission
     * @return bool
     */
    public function hasPermission($permission, $id = null)
    {
        // TODO: Proper implementation
        return true;
    }

    /**
     * Replaces existing content type cache with the one passed in
     *
     * @param object $contentTypeCache Json with an array called 'libraries'
     *  containing the new content type cache that should replace the old one.
     * @throws IllegalObjectTypeException
     */
    public function replaceContentTypeCache($contentTypeCache): void
    {
        $this->contentTypeCacheEntryRepository->removeAll();

        // Create new entries
        foreach ($contentTypeCache->contentTypes as $contentType) {
            $this->contentTypeCacheEntryRepository->add(ContentTypeCacheEntry::create($contentType));
        }

        $this->persistenceManager->persistAll();
    }

    /**
     * Load addon libraries
     *
     * @return array
     */
    public function loadAddons()
    {
        //        $addOns = $this->libraryRepository->findAddOns();
        return [];
    }

    /**
     * Load config for libraries
     *
     * @param array $libraries
     * @return array
     */
    public function getLibraryConfig($libraries = null): array
    {
        // TODO: Implement getLibraryConfig() method.
        return [];
    }

    /**
     * Checks if the given library has a higher version.
     *
     * @param array $library
     * @return bool
     */
    public function libraryHasUpgrade($library)
    {
        return $this->libraryRepository->libraryHasUpgrade($library);
    }

    public function replaceContentHubMetadataCache($metadata, $lang): void
    {
        // TODO: Implement replaceContentHubMetadataCache() method.
    }

    public function getContentHubMetadataCache($lang = 'en'): \JsonSerializable
    {
        // TODO: Implement getContentHubMetadataCache() method.
        return new \SplFixedArray();
    }

    public function getContentHubMetadataChecked($lang = 'en'): ?string
    {
        // TODO: Implement getContentHubMetadataChecked() method.
        return null;
    }

    public function setContentHubMetadataChecked($time, $lang = 'en'): bool
    {
        // TODO: Implement setContentHubMetadataChecked() method.
        return false;
    }

    /**
     * Reset hub organization data.
     * Clears cached hub registration data so the site can re-register
     * with the new hub endpoint.
     */
    public function resetHubOrganizationData(): void
    {
        $this->setOption('hub_secret', null);
        $this->setOption('site_uuid', null);
    }
}
