<?php

declare(strict_types=1);

use In2code\LuxletterImport\Controller\Backend\CsvImportController;

return [
    'lux_import' => [
        'parent' => 'lux_module',
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'csv-import-module',
        'path' => '/module/lux/import',
        'labels' => 'LLL:EXT:luxletter_import/Resources/Private/Language/locallang_db.xlf',
        'extensionName' => 'luxletter_import',
        'controllerActions' => [
            CsvImportController::class => [
                'import',
            ],
        ],
    ],
];
