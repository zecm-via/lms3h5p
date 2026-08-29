<?php

use LMS3\Lms3h5p\Controller\{
    ContentController,
    EditorAjaxController,
    LibraryController
};

return [
    'lms3h5p' => [
        'parent' => 'web',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/lms3h5p',
        'iconIdentifier' => 'tx_lms3h5p',
        'labels' => 'LLL:EXT:lms3h5p/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'lms3h5p',
        'controllerActions' => [
            ContentController::class => [
                'index', 'create', 'new', 'show', 'edit', 'update', 'delete',
            ],
            EditorAjaxController::class => ['index'],
            LibraryController::class => [
                'index', 'show', 'delete', 'refreshContentTypeCache',
            ],
        ],
    ],
];
