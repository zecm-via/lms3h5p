<?php

declare(strict_types=1);

/* * *************************************************************
 *
 *  Copyright notice
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

use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Mutation;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationCollection;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationMode;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;
use TYPO3\CMS\Core\Type\Map;

return Map::fromEntries([
    Scope::backend(),
    new MutationCollection(
        // Fonts
        new Mutation(
            MutationMode::Extend,
            Directive::FontSrc,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceScheme::data
        ),

        // Images
        new Mutation(
            MutationMode::Extend,
            Directive::ImgSrc,
            new UriValue('https://h5p.org/'),
        ),

        // Styles
        new Mutation(
            MutationMode::Extend,
            Directive::StyleSrc,
            new UriValue('https://h5p.org/'),
        ),

        //Scripts
        new Mutation(
            MutationMode::Extend,
            Directive::ScriptSrc,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceKeyword::unsafeInline,
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::ScriptSrc,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceScheme::data,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceKeyword::unsafeEval,
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::ScriptSrc,
            new UriValue('https://www.youtube.com/'),
        ),
        new Mutation(
            MutationMode::Reduce,
            Directive::ScriptSrcElem,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceKeyword::nonceProxy,
        ),
        new Mutation(
            MutationMode::Reduce,
            Directive::ScriptSrc,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceKeyword::nonceProxy,
        ),

        // iFrames
        new Mutation(
            MutationMode::Extend,
            Directive::FrameSrc,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceKeyword::unsafeInline,
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::FrameSrc,
            new UriValue('https://documentation.h5p.com/'),
        ),

        // Media
        new Mutation(
            MutationMode::Extend,
            Directive::MediaSrc,
            new UriValue('https://api.mymemory.translated.net/'),
        ),
        new Mutation(
            MutationMode::Extend,
            Directive::MediaSrc,
            \TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceScheme::blob,
        ),
    ),
]);
