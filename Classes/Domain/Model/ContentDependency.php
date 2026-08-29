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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Content Dependency
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class ContentDependency extends AbstractEntity
{
    protected Content $content;
    protected Library $library;
    protected string $dependencyType;
    protected int $weight;
    protected bool $dropCss;

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): ContentDependency
    {
        $this->content = $content;
        return $this;
    }

    public function getLibrary(): Library
    {
        return $this->library;
    }

    public function setLibrary(Library $library): ContentDependency
    {
        $this->library = $library;
        return $this;
    }

    public function getDependencyType(): string
    {
        return $this->dependencyType;
    }

    public function setDependencyType(string $dependencyType): ContentDependency
    {
        $this->dependencyType = $dependencyType;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): ContentDependency
    {
        $this->weight = $weight;
        return $this;
    }

    public function isDropCss(): bool
    {
        return $this->dropCss;
    }

    public function setDropCss(bool $dropCss): ContentDependency
    {
        $this->dropCss = $dropCss;
        return $this;
    }

    /**
     * Returns an assoc array as expected by
     * @see \H5PCore::getDependenciesFiles
     *
     * @return array
     */
    public function toAssocArray(): array
    {
        // Not all fields from library are expected in this array, but we don't expect conflicts here.
        $libraryData = $this->getLibrary()->toAssocArray();
        return array_merge($libraryData, [
            'dropCss' => $this->isDropCss(),
            'dependencyType' => $this->getDependencyType(),
        ]);
    }
}
