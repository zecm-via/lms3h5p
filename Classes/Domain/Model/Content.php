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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Content
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class Content extends AbstractEntity
{
    protected Library $library;
    protected int $account;
    protected int $createdAt;
    protected int $updatedAt;
    protected string $title;
    protected string $parameters;
    protected string $filtered;
    protected string $slug;
    protected string $embedType;
    protected int $disable;
    protected ?string $contentType = null;
    protected ?string $author = null;
    protected ?string $license = null;
    protected ?string $keywords = null;
    protected ?string $description = null;
    protected ?string $zippedContentFile = null;
    protected ?string $exportFile = null;
    protected ?string $source = null;
    protected ?int $yearFrom = null;
    protected ?int $yearTo = null;
    protected ?string $licenseVersion = null;
    protected ?string $licenseExtras = null;
    protected ?string $authorComments = null;
    protected ?string $changes = null;

    public function getLibrary(): Library
    {
        return $this->library;
    }

    public function setLibrary(Library $library): self
    {
        $this->library = $library;
        return $this;
    }

    public function getAccount(): ?int
    {
        return $this->account;
    }

    public function setAccount(int $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): Content
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updatedAt): Content
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getParameters(): string
    {
        return $this->parameters;
    }

    public function setParameters(string $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getFiltered(): string
    {
        return $this->filtered;
    }

    public function setFiltered(string $filtered): self
    {
        $this->filtered = $filtered;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getEmbedType(): string
    {
        return $this->embedType;
    }

    public function setEmbedType(string $embedType): self
    {
        $this->embedType = $embedType;
        return $this;
    }

    public function getDisable(): int
    {
        return $this->disable;
    }

    public function setDisable(int $disable): self
    {
        $this->disable = $disable;
        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(string $license): self
    {
        $this->license = $license;
        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getZippedContentFile(): ?string
    {
        return $this->zippedContentFile;
    }

    public function setZippedContentFile(string $zippedContentFile): self
    {
        $this->zippedContentFile = $zippedContentFile;
        return $this;
    }

    public function getExportFile(): ?string
    {
        return $this->exportFile;
    }

    public function setExportFile(string $exportFile): self
    {
        $this->exportFile = $exportFile;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getYearFrom(): ?int
    {
        return $this->yearFrom;
    }

    public function setYearFrom(?int $yearFrom): self
    {
        $this->yearFrom = $yearFrom;
        return $this;
    }

    public function getYearTo(): ?int
    {
        return $this->yearTo;
    }

    public function setYearTo(?int $yearTo): self
    {
        $this->yearTo = $yearTo;
        return $this;
    }

    public function getLicenseVersion(): ?string
    {
        return $this->licenseVersion;
    }

    public function setLicenseVersion(?string $licenseVersion): self
    {
        $this->licenseVersion = $licenseVersion;
        return $this;
    }

    public function getLicenseExtras(): ?string
    {
        return $this->licenseExtras;
    }

    public function setLicenseExtras(?string $licenseExtras): self
    {
        $this->licenseExtras = $licenseExtras;
        return $this;
    }

    public function getAuthorComments(): ?string
    {
        return $this->authorComments;
    }

    public function setAuthorComments(?string $authorComments): self
    {
        $this->authorComments = $authorComments;
        return $this;
    }

    public function getChanges(): ?string
    {
        return $this->changes;
    }

    public function setChanges(string $changes): self
    {
        $this->changes = $changes;
        return $this;
    }

    public static function createFromMetadata(array $contentData, Library $library, int $account): Content
    {
        $content = GeneralUtility::makeInstance(Content::class);
        $content->setLibrary($library)
            ->setAccount($account)
            ->setCreatedAt(time())
            ->setUpdatedAt(time())
            ->setTitle($contentData['title'])
            ->setParameters($contentData['params'])
            ->setFiltered('')
            ->setDisable($contentData['disable'])
            ->setLicense($contentData['metadata']->license ?? '')
            ->setAuthor(json_encode($contentData['metadata']->authors))
            ->setChanges(json_encode($contentData['metadata']->changes))
            ->setSlug(''); // Set by h5p later, but must not be null
        /**
         * @see Library::updateFromMetadata()
         */
        $content->determineEmbedType();

        return $content;
    }

    public function updateFromMetadata(array $contentData, Library $library): void
    {
        $this->setUpdatedAt(time())
            ->setTitle($contentData['title'])
            ->setFiltered('')
            ->setLibrary($library);

        if (isset($contentData['params'])) {
            $this->setParameters($contentData['params']);
        }
        if (isset($contentData['disable'])) {
            $this->setDisable($contentData['disable']);
        }
    }

    public function determineEmbedType(): void
    {
        $this->setEmbedType(
            \H5PCore::determineEmbedType('div', $this->getLibrary()->getEmbedTypes())
        );
    }

    /**
     * Returns an associative array containing the content in the form that
     * \H5PCore->filterParameters() expects.
     * @see H5PCore::filterParameters()
     */
    public function toAssocArray(): array
    {
        return [
            'id' => $this->getUid(),
            'title' => $this->getTitle(),
            'library' => $this->getLibrary()->toAssocArray(),
            'slug' => $this->getSlug(),
            'disable' => $this->getDisable(),
            'embedType' => $this->getEmbedType(),
            'params' => $this->getParameters(),
            'filtered' => $this->getFiltered(),
            'metadata' => [
                'title' => $this->getTitle(),
                'authors' => $this->getAuthor() ?? 'null',
                'source' => $this->getSource() ?? 'null',
                'license' => $this->getLicense() ?? 'null',
                'licenseVersion' => $this->getLicenseVersion() ?? 'null',
                'licenseExtras' => $this->getLicenseExtras() ?? 'null',
                'yearFrom' => $this->getYearFrom() ?? 'null',
                'yearTo' => $this->getYearTo() ?? 'null',
                'changes' => $this->getChanges() ?? 'null',
                'authorComments' => $this->getAuthorComments() ?? 'null',
            ],
        ];
    }
}
