<?php

declare(strict_types=1);

namespace LMS3\Lms3h5p\Domain\Repository;

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
use LMS3\Lms3h5p\Domain\Model\Library;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Content Respository
 *
 * @author Sagar Desai <sagar.desai@lms3.de>
 * (c) 2019 LEARNTUBE! GmbH - Contact: mail@learntube.de
 *
 * The H5P software is licensed under the MIT license.
 * Please visit: https://h5p.org/MIT-licensed
 *
 * H5P is a brandmark of Joubel AS - Contact: https://joubel.com/
 *
 * @extends Repository<Content>
 */
class ContentRepository extends Repository
{
    protected $defaultOrderings = [
        'createdAt' => QueryInterface::ORDER_DESCENDING,
    ];

    public function countByLibraryAndSkipped(Library $library, array $skip): int
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('library', $library),
                $query->in('uid', $skip)
            )
        );

        return $query->execute()->count();
    }
}
