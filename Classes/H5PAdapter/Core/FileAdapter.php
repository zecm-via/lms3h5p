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
use H5PCore;
use H5peditorFile;
use H5PFileStorage;
use LMS3\Lms3h5p\Domain\Model\CachedAsset;
use LMS3\Lms3h5p\Domain\Model\Content;
use LMS3\Lms3h5p\Domain\Model\EditorTempFile;
use LMS3\Lms3h5p\Domain\Repository\CachedAssetRepository;
use LMS3\Lms3h5p\Domain\Repository\ContentRepository;
use LMS3\Lms3h5p\Domain\Repository\EditorTempfileRepository;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * File Adapter
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class FileAdapter implements H5PFileStorage
{
    protected array $h5pSettings = [];
    protected PersistenceManager $persistenceManager;
    protected ContentRepository $contentRepository;
    protected CachedAssetRepository $cachedAssetRepository;

    public function __construct()
    {
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->contentRepository = GeneralUtility::makeInstance(ContentRepository::class);
        $this->cachedAssetRepository = GeneralUtility::makeInstance(CachedAssetRepository::class);
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $this->h5pSettings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Lms3h5p',
            'Pi1'
        );
    }

    /**
     * Store the library folder.
     *
     * @param array $library
     *  Library properties
     * @throws Exception
     */
    public function saveLibrary($library): void
    {
        $dest = $this->getFolderPath('libraries') . H5PCore::libraryToFolderName($library);

        // Make sure destination dir doesn't exist
        H5PCore::deleteFileTree($dest);

        // Move library folder
        self::copyFileTree($library['uploadDirectory'], $dest);
    }

    /**
     * Store the content folder.
     *
     * @param string $source
     *  Path on file system to content directory.
     * @param array $content
     *  Content properties
     * @throws Exception
     */
    public function saveContent($source, $content): void
    {
        $dest = $this->getFolderPath('content') . $content['id'];

        // Remove any old content
        H5PCore::deleteFileTree($dest);

        self::copyFileTree($source, $dest);
    }

    /**
     * Remove content folder.
     *
     * @param array $content
     *  Content properties
     */
    public function deleteContent($content): void
    {
        H5PCore::deleteFileTree($this->getFolderPath('content') . $content['id']);
    }

    /**
     * Creates a stored copy of the content folder.
     *
     * @param string $id
     *  Identifier of content to clone.
     * @param int $newId
     *  The cloned content's identifier
     * @throws Exception
     */
    public function cloneContent($id, $newId): void
    {
        $path = $this->getFolderPath('content');
        if (file_exists($path . $id)) {
            self::copyFileTree($path . $id, $path . $newId);
        }
    }

    /**
     * Get path to a new unique tmp folder.
     *
     * @return string
     *  Path
     */
    public function getTmpPath()
    {
        $temp = $this->getFolderPath('temp');
        self::dirReady($temp);
        $path = $temp . uniqid('h5p-');

        return $path;
    }

    /**
     * Fetch content folder and save in target directory.
     *
     * @param int $id
     *  Content identifier
     * @param string $target
     *  Where the content folder will be saved
     * @throws Exception
     */
    public function exportContent($id, $target): void
    {
        $source = $this->getFolderPath('content') . $id;
        if (file_exists($source)) {
            // Copy content folder if it exists
            self::copyFileTree($source, $target);
        } else {
            // No contnet folder, create emty dir for content.json
            self::dirReady($target);
        }
    }

    /**
     * Fetch library folder and save in target directory.
     *
     * @param array $library
     *  Library properties
     * @param string $target
     *  Where the library folder will be saved
     * @throws Exception
     */
    public function exportLibrary($library, $target): void
    {
        $folder = H5PCore::libraryToFolderName($library);
        $srcPath = $this->getFolderPath('libraries') . $folder;
        $destination = $target . DIRECTORY_SEPARATOR . $folder;

        self::copyFileTree($srcPath, $destination);
    }

    /**
     * Save export in file system
     *
     * @param string $source
     *  Path on file system to temporary export file.
     * @param string $filename
     *  Name of export file.
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function saveExport($source, $filename): void
    {
        $this->deleteExport($filename);
        $exportDir = $this->getFolderPath('exports');
        if (!self::dirReady($exportDir)) {
            throw new Exception('Unable to create directory for H5P export file.', 4393869282);
        }

        if (!copy($source, $exportDir . $filename)) {
            throw new Exception('Unable to save H5P export file.', 3982974687);
        }

        // Get the content from the filename again
        /** @var Content $content */
        $content = $this->getContentFromExportFilename($filename);
        $content->setExportFile($filename);
        $this->contentRepository->update($content);
    }

    /**
     * Removes given export file
     *
     * @param string $filename
     */
    public function deleteExport($filename): void
    {
        $target = $this->getFolderPath('exports') . $filename;
        if (file_exists($target)) {
            unlink($target);
        }
    }

    /**
     * Check if the given export file exists
     *
     * @param string $filename
     * @return bool
     */
    public function hasExport($filename)
    {
        $target = $this->getPublicFolderPath('exports') . $filename;

        return file_exists($target);
    }

    /**
     * Will concatenate all JavaScrips and Stylesheets into two files in order
     * to improve page performance.
     *
     * @param array $files
     *  A set of all the assets required for content to display
     * @param string $key
     *  Hashed key for cached asset
     */
    public function cacheAssets(&$files, $key): void
    {
        foreach ($files as $type => $assets) {
            if (empty($assets)) {
                continue; // Skip no assets
            }

            $content = '';
            foreach ($assets as $asset) {
                // Get content from asset file
                $assetContent = '';
                if (file_exists(Environment::getPublicPath() . DIRECTORY_SEPARATOR . ltrim($asset->path, '/'))) {
                    $assetContent = file_get_contents(Environment::getPublicPath() . DIRECTORY_SEPARATOR . ltrim($asset->path, '/'));
                }
                $cssRelPath = preg_replace('/[^\/]+$/', '', $asset->path);
                // ensure CSS contains URLs and not local paths
                $cssRelPath = str_replace($this->h5pSettings['h5pPublicFolder']['path'], $this->h5pSettings['h5pPublicFolder']['url'], $cssRelPath);

                // Get file content and concatenate
                if ($type === 'scripts') {
                    $content .= $assetContent . ";\n";
                } else {
                    // Rewrite relative URLs used inside stylesheets
                    $content .= preg_replace_callback(
                        '/url\([\'"]?([^"\')]+)[\'"]?\)/i',
                        function ($matches) use ($cssRelPath) {
                            if (preg_match("/^(data:|([a-z0-9]+:)?\/)/i", $matches[1]) === 1) {
                                return $matches[0]; // Not relative, skip
                            }

                            return 'url("' . $cssRelPath . $matches[1] . '")';
                        },
                        $assetContent
                    ) . "\n";
                }
            }

            $cachedAssetsDir = $this->getFolderPath('cachedAssets');
            self::dirReady($cachedAssetsDir);
            $ext = ($type === 'scripts' ? 'js' : 'css');
            $outputfile = "{$key}.{$ext}";
            file_put_contents($cachedAssetsDir . $outputfile, $content);
            $files[$type] = [(object)[
                'path'    => PathUtility::getAbsolutePathOfRelativeReferencedFileOrPath(
                    $this->getPublicFolderPath('cachedAssets', false),
                    $outputfile
                ),
                'version' => '',
            ]];

            $cacheAsset = GeneralUtility::makeInstance(CachedAsset::class);
            $cacheAsset->setHashKey($key);
            $cacheAsset->setType($type);
            $this->cachedAssetRepository->add($cacheAsset);
            $this->persistenceManager->persistAll();
        }
    }

    /**
     * Will check if there are cache assets available for content.
     *
     * @param string $key
     *  Hashed key for cached asset
     * @return array
     */
    public function getCachedAssets($key)
    {
        $files = [];
        $jsFileName = "{$key}.js";
        $jsFilePath = $this->getPublicFolderPath('cachedAssets') . $jsFileName;
        $path = $this->getPublicFolderPath('cachedAssets', false);
        if (file_exists($jsFilePath)) {
            $files['scripts'] = [(object)[
                'path'    => $path . $jsFileName,
                'version' => '',
            ]];
        }

        $cssFileName = "{$key}.css";
        $cssFilePath = $this->getPublicFolderPath('cachedAssets') . $cssFileName;
        if (file_exists($cssFilePath)) {
            $files['styles'] = [(object)[
                'path'    => $path . $cssFileName,
                'version' => '',
            ]];
        }

        return empty($files) ? null : $files;
    }

    /**
     * Remove the aggregated cache files.
     *
     * @param array $keys
     *   The hash keys of removed files
     */
    public function deleteCachedAssets($keys): void
    {
        $cachedAssetsPath = $this->getFolderPath('cachedAssets');
        foreach ($keys as $hash) {
            foreach (['js', 'css'] as $ext) {
                $path = "{$cachedAssetsPath}{$hash}.{$ext}";
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }

    /**
     * Read file content of given file and then return it.
     *
     * @param string $file_path
     * @return string contents
     */
    public function getContent($file_path)
    {
        /**
         * This might cause issues if files are not put locally, because the path is generated inside H5P
         * and cannot be modified.
         * @see H5PCore::getDependenciesFiles()
         * @see H5PCore::getDependencyAssets()
         */
        return file_get_contents($file_path);
    }

    /**
     * Save files uploaded through the editor.
     * The files must be marked as temporary until the content form is saved.
     *
     * @param H5peditorFile $file
     * @param int $contentId
     * @return H5peditorFile
     */
    public function saveFile($file, $contentId)
    {
        // Prepare directory
        if (empty($contentId)) {
            // Should be in editor tmp folder
            $path = $this->getFolderPath('editorTempfiles');
        } else {
            // Should be in content folder
            $path = $this->getFolderPath('content') . $contentId . DIRECTORY_SEPARATOR;
        }
        $path .= $file->getType() . 's';

        self::dirReady($path);

        // Add filename to path
        $path .= DIRECTORY_SEPARATOR . $file->getName();

        @copy($_FILES['file']['tmp_name'], $path);

        $editorTempfileRepository = GeneralUtility::makeInstance(EditorTempfileRepository::class);
        $editotTempFile = GeneralUtility::makeInstance(EditorTempFile::class);
        $editotTempFile->setPath(ltrim($path, Environment::getPublicPath()));
        $editotTempFile->setCreatedAt(time());
        $editorTempfileRepository->add($editotTempFile);
        $this->persistenceManager->persistAll();

        return $file;
    }

    /**
     * Copy a file from another content or editor tmp dir.
     * Used when copy pasting content in H5P.
     *
     * @param string $file path + name
     * @param string|int $fromId Content ID or 'editor' string
     * @param int $toId Target Content ID
     */
    public function cloneContentFile($file, $fromId, $toId): void
    {
        // Determine source path
        if ($fromId === 'editor') {
            $sourcepath = $this->getFolderPath('editorTempfiles');
        } else {
            $sourcepath = $this->getFolderPath('content') . $fromId . DIRECTORY_SEPARATOR;
        }
        $sourcepath .= $file;

        // Determine target path
        $filename = basename($file);
        $filedir = str_replace($filename, '', $file);
        $targetpath = "{$this->getFolderPath('content')}{$toId}/{$filedir}";

        // Make sure it's ready
        self::dirReady($targetpath);

        $targetpath .= $filename;

        // Check to see if source exist and if target doesn't
        if (!file_exists($sourcepath) || file_exists($targetpath)) {
            return; // Nothing to copy from or target already exists
        }

        @copy($sourcepath, $targetpath);
    }

    /**
     * Copy a content from one directory to another. Defaults to cloning
     * content from the current temporary upload folder to the editor path.
     *
     * @param string $source path to source directory
     * @param string $contentId Id of content
     *
     * @return object Object containing h5p json and content json data
     * @throws Exception
     */
    public function moveContentDirectory($source, $contentId = null)
    {
        if ($source === null) {
            return (object)[
                'h5pJson'     => '',
                'contentJson' => '',
            ];
        }

        if ($contentId === null || (int)$contentId === 0) {
            $target = $this->getFolderPath('editorTempfiles');
        } else {
            // Use content folder
            $target = $this->getFolderPath('content') . $contentId . DIRECTORY_SEPARATOR;
        }

        $contentSource = $source . DIRECTORY_SEPARATOR . 'content';
        $contentFiles = array_diff(scandir($contentSource), ['.', '..', 'content.json']);
        foreach ($contentFiles as $file) {
            if (is_dir("{$contentSource}/{$file}")) {
                self::copyFileTree("{$contentSource}/{$file}", "{$target}{$file}");
            } else {
                copy("{$contentSource}/{$file}", "{$target}{$file}");
            }
        }

        // Successfully loaded content json of file into editor
        $h5pJson = $this->getContent($source . DIRECTORY_SEPARATOR . 'h5p.json');
        $contentJson = $this->getContent($contentSource . DIRECTORY_SEPARATOR . 'content.json');

        return (object)[
            'h5pJson'     => $h5pJson,
            'contentJson' => $contentJson,
        ];
    }

    /**
     * Checks to see if content has the given file.
     * Used when saving content.
     *
     * @param string $file path + name
     * @param int $contentId
     * @return string|int File path or NULL if not found
     */
    public function getContentFile($file, $contentId)
    {
        $path = $this->getFolderPath('content') . $contentId . DIRECTORY_SEPARATOR . $file;

        return file_exists($path) ? $path : null;
    }

    /**
     * Remove content files that are no longer used.
     * Used when saving content.
     *
     * @param string $file path + name
     * @param int $contentId
     */
    public function removeContentFile($file, $contentId): void
    {
        $path = $this->getFolderPath('content') . $contentId . DIRECTORY_SEPARATOR . $file;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Check if server setup has write permission to
     * the required folders
     *
     * @return bool True if server has the proper write access
     */
    public function hasWriteAccess()
    {
        $h5pPublicFolder = Environment::getPublicPath() . $this->h5pSettings['h5pPublicFolder']['path'];

        return self::dirReady($h5pPublicFolder);
    }

    /**
     * Recursive function for copying directories.
     *
     * @param string $source From path
     * @param string $destination To path
     * @throws Exception
     */
    public static function copyFileTree($source, $destination): void
    {
        if (!self::dirReady($destination)) {
            throw new Exception('unabletocopy', 3643151353);
        }

        $ignoredFiles = self::getIgnoredFiles("{$source}/.h5pignore");

        $dir = opendir($source);
        if ($dir === false) {
            trigger_error('Unable to open directory ' . $source, E_USER_WARNING);

            throw new Exception('unabletocopy', 8344758862);
        }

        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..') && $file !== '.git' && $file !== '.gitignore' && !in_array($file, $ignoredFiles, true)) {
                if (is_dir("{$source}/{$file}")) {
                    self::copyFileTree("{$source}/{$file}", "{$destination}/{$file}");
                } else {
                    copy("{$source}/{$file}", "{$destination}/{$file}");
                }
            }
        }
        closedir($dir);
    }

    /**
     * Recursive function that makes sure the specified directory exists and
     * is writable.
     *
     * @param string $path
     * @return bool
     */
    public static function dirReady($path)
    {
        if (!file_exists($path)) {
            $parent = preg_replace("/\/[^\/]+\/?$/", '', $path);
            if (!self::dirReady($parent)) {
                return false;
            }

            mkdir($path, 0o777, true);
        }

        if (!is_dir($path)) {
            trigger_error('Path is not a directory ' . $path, E_USER_WARNING);

            return false;
        }

        if (!is_writable($path)) {
            trigger_error('Unable to write to ' . $path . ' – check directory permissions –', E_USER_WARNING);

            return false;
        }

        return true;
    }

    /**
     * Retrieve array of file names from file.
     *
     * @param string $file
     * @return array Array with files that should be ignored
     */
    private static function getIgnoredFiles($file)
    {
        if (file_exists($file) === false) {
            return [];
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            return [];
        }

        return preg_split('/\s+/', $contents);
    }

    /**
     * The filename we get here is $content['slug'] . '-' . $content['id'] . '.h5p').
     * As we don't want to search the resource repository by filename (there could be
     * other files with the same name), we will extract the content ID from it again,
     * retrieve the Content based on that.
     *
     * @param string $filename
     * @return Content|null
     */
    private function getContentFromExportFilename(string $filename)
    {
        $parts = explode('-', str_replace('.h5p', '', $filename));
        $contentId = end($parts);
        return $this->contentRepository->findByUid((int)$contentId);
    }

    /**
     * Get folder path
     *
     * @param string $folderName
     * @param bool $absolutePath
     * @return string
     */
    private function getFolderPath($folderName, $absolutePath = true)
    {
        if ($absolutePath === false) {
            return $this->h5pSettings['h5pPublicFolder']['path'] . $this->h5pSettings['subFolders'][$folderName] . DIRECTORY_SEPARATOR;
        }

        return Environment::getPublicPath() . $this->h5pSettings['h5pPublicFolder']['path'] . $this->h5pSettings['subFolders'][$folderName] . DIRECTORY_SEPARATOR;
    }

    /**
     * Get folder path
     *
     * @param string $folderName
     * @param bool $absolutePath
     * @return string
     */
    private function getPublicFolderPath($folderName, $absolutePath = true)
    {
        if ($absolutePath === false) {
            return $this->h5pSettings['h5pPublicFolder']['url'] . $this->h5pSettings['subFolders'][$folderName] . DIRECTORY_SEPARATOR;
        }

        return Environment::getPublicPath() . $this->h5pSettings['h5pPublicFolder']['url'] . $this->h5pSettings['subFolders'][$folderName] . DIRECTORY_SEPARATOR;
    }

    /**
     * Check if the library has a presave.js in the root folder
     *
     * @param string $libraryFolder
     * @param string $developmentPath
     * @return bool
     */
    public function hasPresave($libraryFolder, $developmentPath = null)
    {
        $srcPath = $this->getFolderPath('libraries') . $libraryFolder;
        $filePath = realpath($srcPath . DIRECTORY_SEPARATOR . 'presave.js');
        return file_exists($filePath);
    }

    /**
     * Check if upgrades script exist for library.
     *
     * @param array $library
     * @return string Relative path
     */
    public function getUpgradeScript($library)
    {
        $upgradeScript = \H5PCore::libraryToFolderName($library) . '/upgrades.js';
        $upgradesFilePath = $this->getFolderPath('libraries', false) . $upgradeScript;

        if (file_exists(Environment::getPublicPath() . $upgradesFilePath)) {
            return 'libraries/' . $upgradeScript;
        }
        return '';
    }

    /**
     * Store the given stream into the given file.
     *
     * @param string $path
     * @param string $file
     * @param resource $stream
     * @return bool
     */
    public function saveFileFromZip($path, $file, $stream)
    {
        $filePath = $path . DIRECTORY_SEPARATOR . $file;

        // Make sure the directory exists first
        $matches = [];
        preg_match('/(.+)\/[^\/]*$/', $filePath, $matches);
        self::dirReady($matches[1]);

        // Store in local storage folder
        return file_put_contents($filePath, $stream);
    }

    public function deleteLibrary($library): void
    {
        // TODO: Implement deleteLibrary() method.
    }
}
