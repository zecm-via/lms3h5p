<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Content Type Cache Entry',
        'label' => 'machine_name',
        'hideTable' => 1,
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                machine_name, major_version, minor_version, patch_version, h5p_major_version, h5p_minor_version, title, summary, description, icon
            ',
        ],
    ],
    'columns' => [
        'machine_name' => [
            'label' => 'Machine Name',
            'config' => [
                'type' => 'input',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'major_version' => [
            'label' => 'Major Version',
            'config' => [
                'type' => 'input',
            ],
        ],
        'minor_version' => [
            'label' => 'Minor Version',
            'config' => [
                'type' => 'input',
            ],
        ],
        'patch_version' => [
            'label' => 'Patch Version',
            'config' => [
                'type' => 'input',
            ],
        ],
        'h5p_major_version' => [
            'label' => 'H5P Major Version',
            'config' => [
                'type' => 'input',
            ],
        ],
        'h5p_minor_version' => [
            'label' => 'H5P Minor Version',
            'config' => [
                'type' => 'input',
            ],
        ],
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
            ],
        ],
        'summary' => [
            'label' => 'Summary',
            'config' => [
                'type' => 'text',
            ],
        ],
        'description' => [
            'label' => 'Description',
            'config' => [
                'type' => 'text',
            ],
        ],
        'icon' => [
            'label' => 'Icon',
            'config' => [
                'type' => 'input',
            ],
        ],
        'created_at' => [
            'label' => 'Created At',
            'config' => [
                'type' => 'input',
            ],
        ],
        'updated_at' => [
            'label' => 'Updated At',
            'config' => [
                'type' => 'input',
            ],
        ],
        'is_recommended' => [
            'label' => 'Is Recommended?',
            'config' => [
                'type' => 'input',
            ],
        ],
        'popularity' => [
            'label' => 'Popularity',
            'config' => [
                'type' => 'input',
            ],
        ],
        'screenshots' => [
            'label' => 'Screenshots',
            'config' => [
                'type' => 'input',
            ],
        ],
        'license' => [
            'label' => 'License',
            'config' => [
                'type' => 'input',
            ],
        ],
        'example' => [
            'label' => 'Example',
            'config' => [
                'type' => 'input',
            ],
        ],
        'tutorial' => [
            'label' => 'Tutorial',
            'config' => [
                'type' => 'input',
            ],
        ],
        'keywords' => [
            'label' => 'Keywords',
            'config' => [
                'type' => 'input',
            ],
        ],
        'categories' => [
            'label' => 'Categories',
            'config' => [
                'type' => 'input',
            ],
        ],
        'owner' => [
            'label' => 'Owner',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
];
