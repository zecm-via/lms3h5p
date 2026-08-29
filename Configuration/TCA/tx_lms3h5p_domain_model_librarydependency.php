<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Library Dependency',
        'label' => 'uid_local',
        'label_alt' => 'uid_foreign, dependency_type',
        'label_alt_force' => true,
        'sortby' => 'sorting',
        'hideTable' => 1,
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                uid_local, uid_foreign, dependency_type
            ',
        ],
    ],
    'columns' => [
        'uid_local' => [
            'label' => 'Library',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_lms3h5p_domain_model_library',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'uid_foreign' => [
            'label' => 'Required Library',
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
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'Editor',
                        'value' => 'editor',
                    ],
                    [
                        'label' => 'Preloaded',
                        'value' => 'preloaded',
                    ],
                    [
                        'label' => 'Dynamic',
                        'value' => 'dynamic',
                    ],
                ],
            ],
        ],
    ],
];
