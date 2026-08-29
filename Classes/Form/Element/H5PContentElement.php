<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Form\Element;

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

use LMS3\Lms3h5p\Domain\Model\Content;
use LMS3\Lms3h5p\Domain\Repository\ContentRepository;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ContentEmbedController
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class H5PContentElement extends AbstractFormElement
{
    /**
     * Handler for single nodes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render(): array
    {
        $parameterArray = $this->data['parameterArray'];
        $itemValue = $parameterArray['itemFormElValue'];
        $itemName = $parameterArray['itemFormElName'];
        $result = $this->initializeResultArray();

        /** @var ContentRepository $contentRepository */
        $contentRepository = GeneralUtility::makeInstance(ContentRepository::class);
        $contentRepository->setDefaultQuerySettings(
            $contentRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false)
        );
        $contents = $contentRepository->findAll();
        $options = '';
        /** @var Content $content */
        foreach ($contents as $content) {
            if ($content->getUid() === (int)$itemValue) {
                $options .= '<option value="' . $content->getUid() . '" selected="selected">' . $content->getTitle() . '</option>';
            } else {
                $options .= '<option value="' . $content->getUid() . '">' . $content->getTitle() . '</option>';
            }
        }

        $html = [];
        $html[] = '<div class="formengine-field-item">';
        $html[] =   '<div class="form-wizards-wrap">';
        $html[] =       '<div class="form-wizards-element">';
        $html[] =           '<div class="form-control-wrap">';
        $html[] =               '<select class="form-control" name="' . $itemName . '">';
        $html[] =                   $options;
        $html[] =               '</select>';
        $html[] =           '</div>';
        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';
        $result['html'] = implode("\n", $html);

        return $result;
    }
}
