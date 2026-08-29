<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx_lms3h5p' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:lms3h5p/Resources/Public/Icons/h5p.svg',
    ],
    'tx-lms3h5p-svgicon' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
];
