<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Cache Asset',
        'label' => 'library',
        'label_alt' => 'type, hash_key',
        'label_alt_force' => true,
        'hideTable' => 1,
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                library, hash_key, type
            ',
        ],
    ],
    'columns' => [
        'library' => [
            'label' => 'Library',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_lms3h5p_domain_model_library',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'hash_key' => [
            'label' => 'Hash key',
            'config' => [
                'type' => 'input',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'type' => [
            'label' => 'Type',
            'config' => [
                'type' => 'input',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
    ],
];
