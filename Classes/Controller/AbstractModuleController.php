<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Controller;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
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

use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * AbstractModuleController
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 */
class AbstractModuleController extends ActionController
{
    protected function generateMenu(ModuleTemplate $moduleTemplate): void
    {
        $menuItems = $this->getMenuItems();
        $menu = $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('H5PModuleMenu');

        foreach ($menuItems as $menuItemConfig) {
            if ($this->request->getControllerName() === $menuItemConfig['controller']) {
                $isActive = true;
            } else {
                $isActive = false;
            }
            $menuItem = $menu->makeMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($this->getHref($menuItemConfig['controller'], $menuItemConfig['action']))
                ->setActive($isActive);

            $menu->addMenuItem($menuItem);
        }

        $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function getHref(string $controller, string $action, array $parameters = []): string
    {
        return $this->uriBuilder->uriFor($action, $parameters, $controller);
    }

    protected function translate(string $key): string
    {
        return LocalizationUtility::translate('LLL:EXT:lms3h5p/Resources/Private/Language/locallang.xlf:' . $key, 'lms3h5p');
    }

    private function getMenuItems(): array
    {
        return [
            'content' => [
                'controller' => 'Content',
                'action' => 'index',
                'label' => $this->translate('content'),
            ],
            'library' => [
                'controller' => 'Library',
                'action' => 'index',
                'label' => $this->translate('libraries'),
            ],
        ];
    }
}
