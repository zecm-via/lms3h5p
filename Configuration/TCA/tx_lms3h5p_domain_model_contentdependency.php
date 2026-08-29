<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Content Dependency',
        'label' => 'library',
        'label_alt' => 'dependency_type',
        'label_alt_force' => true,
        'hideTable' => 1,
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                content, library, dependency_type, weight, drop_css
            ',
        ],
    ],
    'columns' => [
        'content' => [
            'label' => 'Content',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_lms3h5p_domain_model_content',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
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
        'dependency_type' => [
            'label' => 'Dependency Type',
            'config' => [
                'type' => 'input',
            ],
        ],
        'weight' => [
            'label' => 'Type',
            'config' => [
                'type' => 'input',
            ],
        ],
        'drop_css' => [
            'label' => 'Drop Css',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
];
