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
 * Library Translation
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class LibraryTranslation extends AbstractEntity
{
    protected Library $library;
    protected string $languageCode;
    protected string $translation;

    public function getLibrary(): Library
    {
        return $this->library;
    }

    public function setLibrary(Library $library): LibraryTranslation
    {
        $this->library = $library;
        return $this;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): LibraryTranslation
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function setTranslation(string $translation): LibraryTranslation
    {
        $this->translation = $translation;
        return $this;
    }

    public static function create(Library $library, string $languageCode, string $translation): LibraryTranslation
    {
        $translationInstance = GeneralUtility::makeInstance(LibraryTranslation::class);
        $translationInstance->setLibrary($library)
            ->setLanguageCode($languageCode)
            ->setTranslation($translation);

        return $translationInstance;
    }
}
