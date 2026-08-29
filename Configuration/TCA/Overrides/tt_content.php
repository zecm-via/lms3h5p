<?php

defined('TYPO3') or die();

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Register Plugin
$contentTypeName = ExtensionUtility::registerPlugin(
    'Lms3h5p',
    'Pi1',
    'LLL:EXT:lms3h5p/Resources/Private/Language/locallang_db.xlf:tx_lms3h5p_domain_model_pi1.name',
    'tx-lms3h5p-svgicon',
    'LMS3',
    'LLL:EXT:lms3h5p/Resources/Private/Language/locallang_db.xlf:tx_lms3h5p_domain_model_pi1.description',
);

// Register Flexform
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:lms3h5p/Configuration/FlexForms/FlexFormPi1.xml',
    $contentTypeName
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:plugin, pi_flexform',
    $contentTypeName,
    'after:palette:headers'
);
