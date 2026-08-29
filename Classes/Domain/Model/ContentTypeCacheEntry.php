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

use DateTime;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Content Type Cache Entry
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class ContentTypeCacheEntry extends AbstractEntity
{
    protected string $machineName;
    protected int $majorVersion;
    protected int $minorVersion;
    protected int $patchVersion;
    protected int $h5pMajorVersion;
    protected int $h5pMinorVersion;
    protected string $title;
    protected string $summary;
    protected string $description;
    protected string $icon;
    protected int $createdAt;
    protected int $updatedAt;
    protected bool $isRecommended;
    protected int $popularity;
    protected ?string $screenshots = null;
    protected ?string $license = null;
    protected string $example;
    protected ?string $tutorial = null;
    protected ?string $keywords = null;
    protected ?string $categories = null;
    protected ?string $owner = null;

    public function getMachineName(): string
    {
        return $this->machineName;
    }

    public function setMachineName(string $machineName): ContentTypeCacheEntry
    {
        $this->machineName = $machineName;
        return $this;
    }

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function setMajorVersion(int $majorVersion): ContentTypeCacheEntry
    {
        $this->majorVersion = $majorVersion;
        return $this;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function setMinorVersion(int $minorVersion): ContentTypeCacheEntry
    {
        $this->minorVersion = $minorVersion;
        return $this;
    }

    public function getPatchVersion(): int
    {
        return $this->patchVersion;
    }

    public function setPatchVersion(int $patchVersion): ContentTypeCacheEntry
    {
        $this->patchVersion = $patchVersion;
        return $this;
    }

    public function getH5pMajorVersion(): int
    {
        return $this->h5pMajorVersion;
    }

    public function setH5pMajorVersion(int $h5pMajorVersion): ContentTypeCacheEntry
    {
        $this->h5pMajorVersion = $h5pMajorVersion;
        return $this;
    }

    public function getH5pMinorVersion(): int
    {
        return $this->h5pMinorVersion;
    }

    public function setH5pMinorVersion(int $h5pMinorVersion): ContentTypeCacheEntry
    {
        $this->h5pMinorVersion = $h5pMinorVersion;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): ContentTypeCacheEntry
    {
        $this->title = $title;
        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): ContentTypeCacheEntry
    {
        $this->summary = $summary;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ContentTypeCacheEntry
    {
        $this->description = $description;
        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): ContentTypeCacheEntry
    {
        $this->icon = $icon;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): ContentTypeCacheEntry
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): ContentTypeCacheEntry
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isRecommended(): bool
    {
        return $this->isRecommended;
    }

    public function setIsRecommended(bool $isRecommended): ContentTypeCacheEntry
    {
        $this->isRecommended = $isRecommended;
        return $this;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function setPopularity(int $popularity): ContentTypeCacheEntry
    {
        $this->popularity = $popularity;
        return $this;
    }

    public function getScreenshots(): string
    {
        return $this->screenshots;
    }

    public function setScreenshots(string $screenshots): ContentTypeCacheEntry
    {
        $this->screenshots = $screenshots;
        return $this;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function setLicense(string $license): ContentTypeCacheEntry
    {
        $this->license = $license;
        return $this;
    }

    public function getExample(): string
    {
        return $this->example;
    }

    public function setExample(string $example): ContentTypeCacheEntry
    {
        $this->example = $example;
        return $this;
    }

    public function getTutorial(): string
    {
        return $this->tutorial;
    }

    public function setTutorial(string $tutorial): ContentTypeCacheEntry
    {
        $this->tutorial = $tutorial;
        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): ContentTypeCacheEntry
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): ContentTypeCacheEntry
    {
        $this->categories = $categories;
        return $this;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): ContentTypeCacheEntry
    {
        $this->owner = $owner;
        return $this;
    }

    public static function create(\stdClass $contentTypeCacheObject): self
    {
        $createdAt = new DateTime($contentTypeCacheObject->createdAt);
        $updatedAt = new DateTime($contentTypeCacheObject->updatedAt);
        $entry = GeneralUtility::makeInstance(ContentTypeCacheEntry::class);
        $entry->setMachineName($contentTypeCacheObject->id)
            ->setMajorVersion((int)$contentTypeCacheObject->version->major)
            ->setMinorVersion((int)$contentTypeCacheObject->version->minor)
            ->setPatchVersion((int)$contentTypeCacheObject->version->patch)
            ->setH5pMajorVersion((int)$contentTypeCacheObject->coreApiVersionNeeded->major)
            ->setH5pMinorVersion((int)$contentTypeCacheObject->coreApiVersionNeeded->minor)
            ->setTitle($contentTypeCacheObject->title)
            ->setSummary($contentTypeCacheObject->summary)
            ->setDescription($contentTypeCacheObject->description)
            ->setIcon($contentTypeCacheObject->icon)
            ->setCreatedAt($createdAt->getTimestamp())
            ->setUpdatedAt($updatedAt->getTimestamp())
            ->setIsRecommended($contentTypeCacheObject->isRecommended)
            ->setPopularity($contentTypeCacheObject->popularity)
            ->setScreenshots(
                json_encode($contentTypeCacheObject->screenshots)
            )
            ->setLicense(
                json_encode($contentTypeCacheObject->license ?? [])
            )
            ->setExample($contentTypeCacheObject->example)
            ->setTutorial(
                $contentTypeCacheObject->tutorial ?? ''
            )
            ->setKeywords(
                json_encode($contentTypeCacheObject->keywords ?? [])
            )
            ->setCategories(
                json_encode($contentTypeCacheObject->categories ?? [])
            )
            ->setOwner($contentTypeCacheObject->owner);

        return $entry;
    }

    /**
     * Returns the library cache entry in a format that H5P expects.
     *
     * @return \stdClass
     */
    public function toStdClass(): \stdClass
    {
        return (object)[
            'id' => $this->getUid(),
            'machine_name' => $this->getMachineName(),
            'major_version' => $this->getMajorVersion(),
            'minor_version' => $this->getMinorVersion(),
            'patch_version' => $this->getPatchVersion(),
            'h5p_major_version' => $this->getH5pMajorVersion(),
            'h5p_minor_version' => $this->getH5pMinorVersion(),
            'title' => $this->getTitle(),
            'summary' => $this->getSummary(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'is_recommended' => $this->isRecommended(),
            'popularity' => $this->getPopularity(),
            'screenshots' => $this->getScreenshots(),
            'license' => $this->getLicense(),
            'owner' => $this->getOwner(),
        ];
    }
}
