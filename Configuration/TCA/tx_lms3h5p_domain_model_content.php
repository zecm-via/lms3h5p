<?php

return [
    'ctrl' => [
        'title' => 'LMS3 H5P Content',
        'label' => 'title',
        'iconfile' => 'EXT:lms3h5p/Resources/Public/Icons/Extension.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                title, library, slug
            ',
        ],
    ],
    'columns' => [
        'title' => [
            'label' => 'Title',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'max' => 50,
                'required' => true,
                'eval' => 'trim',
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
        'account' => [
            'label' => 'Account',
            'config' => [
                'type' => 'input',
            ],
        ],
        'zipped_content_file' => [
            'label' => 'Zipped Content File',
            'config' => [
                'type' => 'input',
            ],
        ],
        'export_file' => [
            'label' => 'Export File',
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
        'parameters' => [
            'label' => 'Parameters',
            'config' => [
                'type' => 'input',
            ],
        ],
        'filtered' => [
            'label' => 'Filtered',
            'config' => [
                'type' => 'input',
            ],
        ],
        'slug' => [
            'label' => 'Slug',
            'config' => [
                'type' => 'input',
            ],
        ],
        'embed_type' => [
            'label' => 'Embed Type',
            'config' => [
                'type' => 'input',
            ],
        ],
        'disable' => [
            'label' => 'Disable',
            'config' => [
                'type' => 'input',
            ],
        ],
        'content_type' => [
            'label' => 'Content type',
            'config' => [
                'type' => 'input',
            ],
        ],
        'author' => [
            'label' => 'Author',
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
        'description' => [
            'label' => 'Description',
            'config' => [
                'type' => 'text',
            ],
        ],
        'source' => [
            'label' => 'Source',
            'config' => [
                'type' => 'input',
            ],
        ],
        'year_from' => [
            'label' => 'Year From',
            'config' => [
                'type' => 'input',
            ],
        ],
        'year_to' => [
            'label' => 'Year To',
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
        'license_version' => [
            'label' => 'License Version',
            'config' => [
                'type' => 'input',
            ],
        ],
        'license_extras' => [
            'label' => 'License Extras',
            'config' => [
                'type' => 'input',
            ],
        ],
        'author_comments' => [
            'label' => 'Author Comments',
            'config' => [
                'type' => 'input',
            ],
        ],
        'changes' => [
            'label' => 'changes',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
];
