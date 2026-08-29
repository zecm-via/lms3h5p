<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Library Translation',
        'label' => 'library',
        'label_alt' => 'language_code',
        'label_alt_force' => true,
        'hideTable' => 1,
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                library, language_code, translation
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
        'language_code' => [
            'label' => 'Language Code',
            'config' => [
                'type' => 'input',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'translation' => [
            'label' => 'Translation',
            'config' => [
                'type' => 'text',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
    ],
];
