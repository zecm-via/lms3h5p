<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Domain\Model;

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

use LMS3\Lms3h5p\Domain\Repository\ContentDependencyRepository;
use LMS3\Lms3h5p\Domain\Repository\ContentRepository;
use LMS3\Lms3h5p\Domain\Repository\LibraryDependencyRepository;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Library
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class Library extends AbstractEntity
{
    protected string $name;
    protected string $title;
    protected int $majorVersion;
    protected int $minorVersion;
    protected int $patchVersion;
    protected bool $runnable;
    protected bool $restricted;
    protected bool $fullscreen;
    protected string $embedTypes;
    protected string $preloadedJs;
    protected string $preloadedCss;
    protected string $dropLibraryCss;
    protected string $semantics;
    protected string $tutorialUrl;
    protected bool $hasIcon;
    protected ?string $metaDataSettings = null;
    protected ?string $addTo = null;
    protected int $createdAt;
    protected int $updatedAt;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Library
    {
        $this->name = $name;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Library
    {
        $this->title = $title;
        return $this;
    }

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function setMajorVersion(int $majorVersion): Library
    {
        $this->majorVersion = $majorVersion;
        return $this;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function setMinorVersion(int $minorVersion): Library
    {
        $this->minorVersion = $minorVersion;
        return $this;
    }

    public function getPatchVersion(): int
    {
        return $this->patchVersion;
    }

    public function setPatchVersion(int $patchVersion): Library
    {
        $this->patchVersion = $patchVersion;
        return $this;
    }

    public function isRunnable(): bool
    {
        return $this->runnable;
    }

    public function setRunnable(bool $runnable): Library
    {
        $this->runnable = $runnable;
        return $this;
    }

    public function isRestricted(): bool
    {
        return $this->restricted;
    }

    public function setRestricted(bool $restricted): Library
    {
        $this->restricted = $restricted;
        return $this;
    }

    public function isFullscreen(): bool
    {
        return $this->fullscreen;
    }

    public function setFullscreen(bool $fullscreen): Library
    {
        $this->fullscreen = $fullscreen;
        return $this;
    }

    public function getEmbedTypes(): string
    {
        return $this->embedTypes;
    }

    public function setEmbedTypes(string $embedTypes): Library
    {
        $this->embedTypes = $embedTypes;
        return $this;
    }

    public function getPreloadedJs(): string
    {
        return $this->preloadedJs;
    }

    public function setPreloadedJs(string $preloadedJs): Library
    {
        $this->preloadedJs = $preloadedJs;
        return $this;
    }

    public function getPreloadedCss(): string
    {
        return $this->preloadedCss;
    }

    public function setPreloadedCss(string $preloadedCss): Library
    {
        $this->preloadedCss = $preloadedCss;
        return $this;
    }

    public function getDropLibraryCss(): string
    {
        return $this->dropLibraryCss;
    }

    public function setDropLibraryCss(string $dropLibraryCss): Library
    {
        $this->dropLibraryCss = $dropLibraryCss;
        return $this;
    }

    public function getSemantics(): string
    {
        return $this->semantics;
    }

    public function setSemantics(string $semantics): Library
    {
        $this->semantics = $semantics;
        return $this;
    }

    public function getTutorialUrl(): string
    {
        return $this->tutorialUrl;
    }

    public function setTutorialUrl(string $tutorialUrl): Library
    {
        $this->tutorialUrl = $tutorialUrl;
        return $this;
    }

    public function isHasIcon(): bool
    {
        return $this->hasIcon;
    }

    public function setHasIcon(bool $hasIcon): Library
    {
        $this->hasIcon = $hasIcon;
        return $this;
    }

    public function getMetaDataSettings(): ?string
    {
        return $this->metaDataSettings;
    }

    public function setMetaDataSettings(?string $metaDataSettings): Library
    {
        $this->metaDataSettings = $metaDataSettings;
        return $this;
    }

    public function getAddTo(): ?string
    {
        return $this->addTo;
    }

    public function setAddTo(?string $addTo): Library
    {
        $this->addTo = $addTo;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): Library
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): Library
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public static function createFromMetadata(array &$libraryData): Library
    {
        $libraryData['__preloadedJs'] = self::pathsToCsv($libraryData, 'preloadedJs');
        $libraryData['__preloadedCss'] = self::pathsToCsv($libraryData, 'preloadedCss');

        $libraryData['__dropLibraryCss'] = '0';
        if (isset($libraryData['dropLibraryCss'])) {
            $libs = [];
            foreach ($libraryData['dropLibraryCss'] as $lib) {
                $libs[] = $lib['machineName'];
            }
            $libraryData['__dropLibraryCss'] = implode(', ', $libs);
        }

        $libraryData['__embedTypes'] = '';
        if (isset($libraryData['embedTypes'])) {
            $libraryData['__embedTypes'] = implode(', ', $libraryData['embedTypes']);
        }
        if (!isset($libraryData['semantics'])) {
            $libraryData['semantics'] = '';
        }
        if (!isset($libraryData['hasIcon'])) {
            $libraryData['hasIcon'] = 0;
        }
        if (!isset($libraryData['fullscreen'])) {
            $libraryData['fullscreen'] = 0;
        }

        $library = new self();
        $library->updateFromMetadata($libraryData);
        $library->setCreatedAt(time())
            ->setUpdatedAt(time())
            ->setRestricted(false)
            ->setTutorialUrl('');

        return $library;
    }

    public function updateFromMetadata(array $libraryData): void
    {
        $this->setUpdatedAt(time())
            ->setName($libraryData['machineName'])
            ->setTitle($libraryData['title'])
            ->setMajorVersion($libraryData['majorVersion'])
            ->setMinorVersion($libraryData['minorVersion'])
            ->setPatchVersion($libraryData['patchVersion'])
            ->setRunnable((bool)$libraryData['runnable'])
            ->setHasIcon((bool)$libraryData['hasIcon'])
            ->setMetaDataSettings($libraryData['metadataSettings'] ?? null)
            ->setAddTo(isset($libraryData['addTo']) ? json_encode($libraryData['addTo']) : null);
        if (isset($libraryData['semantics'])) {
            $this->setSemantics($libraryData['semantics']);
        }
        if (isset($libraryData['fullscreen'])) {
            $this->setFullscreen((bool)$libraryData['fullscreen']);
        }
        if (isset($libraryData['__embedTypes'])) {
            $this->setEmbedTypes($libraryData['__embedTypes']);
            /** @var Content $content */
            foreach ($this->getContents() as $content) {
                /** Embed types might have changed, so we trigger a redetermination */
                $content->determineEmbedType();
            }
        }

        $libraryData['__preloadedJs'] = self::pathsToCsv($libraryData, 'preloadedJs');
        $libraryData['__preloadedCss'] = self::pathsToCsv($libraryData, 'preloadedCss');

        $this->setPreloadedJs($libraryData['__preloadedJs']);
        $this->setPreloadedCss($libraryData['__preloadedCss']);

        $libraryData['__dropLibraryCss'] = '0';
        if (isset($libraryData['dropLibraryCss'])) {
            $libs = [];
            foreach ($libraryData['dropLibraryCss'] as $lib) {
                $libs[] = $lib['machineName'];
            }
            $libraryData['__dropLibraryCss'] = implode(', ', $libs);
        }
        $this->setDropLibraryCss($libraryData['__dropLibraryCss']);
    }

    /**
     * Returns this library as a stdClass object in a format that H5P expects
     * when it calls the method:
     * @see \H5peditorStorage::getLibraries()
     * @return \stdClass
     */
    public function toStdClass(): \stdClass
    {
        return (object)$this->toAssocArray();
    }

    /**
     * Returns an associative array containing the library in the form that
     * H5PFramework->loadLibrary is expected to return.
     * @see H5PFramework::loadLibrary()
     */
    public function toAssocArray(): array
    {
        $libraryArray = [
            'id' => $this->getUid(),
            'libraryId' => $this->getUid(),
            'name' => $this->getName(),
            'machineName' => $this->getName(),
            'title' => $this->getTitle(),
            'major_version' => $this->getMajorVersion(),
            'majorVersion' => $this->getMajorVersion(),
            'minor_version' => $this->getMinorVersion(),
            'minorVersion' => $this->getMinorVersion(),
            'patch_version' => $this->getPatchVersion(),
            'patchVersion' => $this->getPatchVersion(),
            'embedTypes' => $this->getEmbedTypes(),
            'preloadedJs' => $this->getPreloadedJs(),
            'preloadedCss' => $this->getPreloadedCss(),
            'dropLibraryCss' => $this->getDropLibraryCss(),
            'fullscreen' => $this->isFullscreen(),
            'runnable' => $this->isRunnable(),
            'semantics' => $this->getSemantics(),
            'hasIcon' => $this->isHasIcon(),
            'metadataSettings' => $this->getMetaDataSettings(),
        ];

        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            $dependencies = $this->getLibraryDependencies();
            /** @var LibraryDependency $dependency */
            foreach ($dependencies as $dependency) {
                $libraryArray[$dependency->getDependencyType() . 'Dependencies'][] = [
                    'machineName' => $dependency->getRequiredLibrary()->getName(),
                    'majorVersion' => $dependency->getRequiredLibrary()->getMajorVersion(),
                    'minorVersion' => $dependency->getRequiredLibrary()->getMinorVersion(),
                ];
            }
        }

        return $libraryArray;
    }

    /**
     * @return QueryResultInterface<int, Content>
     */
    public function getContents(): QueryResultInterface
    {
        $contentRepository = GeneralUtility::makeInstance(ContentRepository::class);
        $contentRepository->setDefaultQuerySettings(
            $contentRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        return $contentRepository->findBy(['library' => $this->getUid()]);
    }

    /**
     * @return QueryResultInterface<int, LibraryDependency>
     */
    public function getLibraryDependencies(): QueryResultInterface
    {
        $dependencyRepository = GeneralUtility::makeInstance(LibraryDependencyRepository::class);
        $dependencyRepository->setDefaultQuerySettings(
            $dependencyRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        return $dependencyRepository->findBy(['uid_local' => $this->getUid()]);
    }

    /**
     * @return QueryResultInterface<int, LibraryDependency>
     */
    public function getDependentLibraries(): QueryResultInterface
    {
        $dependencyRepository = GeneralUtility::makeInstance(LibraryDependencyRepository::class);
        $dependencyRepository->setDefaultQuerySettings(
            $dependencyRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        return $dependencyRepository->findBy(['uid_foreign' => $this->getUid()]);
    }

    /**
     * @return QueryResultInterface<int, ContentDependency>
     */
    public function getContentDependencies(): QueryResultInterface
    {
        $contentDependencyRepository = GeneralUtility::makeInstance(ContentDependencyRepository::class);
        $contentDependencyRepository->setDefaultQuerySettings(
            $contentDependencyRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );

        return $contentDependencyRepository->findBy(['library' => $this->getUid()]);
    }

    /**
     * @return string
     */
    public function getVersionString(): string
    {
        return $this->getMajorVersion() . '.' . $this->getMinorVersion() . '.' . $this->getPatchVersion();
    }

    /**
     * Convert list of file paths to csv
     *
     * @param array $library Library data as found in library.json files
     * @param string $key Key that should be found in $libraryData
     * @return string File paths separated by ', '
     */
    private static function pathsToCsv(array $library, string $key): string
    {
        if (isset($library[$key])) {
            $paths = [];
            foreach ($library[$key] as $file) {
                $paths[] = $file['path'];
            }
            return implode(', ', $paths);
        }
        return '';
    }

}
