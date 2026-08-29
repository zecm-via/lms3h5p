<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Setting',
        'label' => 'config_key',
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                config_key, config_value
            ',
        ],
    ],
    'columns' => [
        'config_key' => [
            'label' => 'Config Key',
            'config' => [
                'type' => 'input',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'config_value' => [
            'label' => 'Config Value',
            'config' => [
                'type' => 'text',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
    ],
];
