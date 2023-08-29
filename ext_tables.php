<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(
    function () {
        /**
         * Register Icons
         */
        $icons = [
            'csv-import-module' => 'EXT:luxletter_import/Resources/Public/Icons/csv_import.svg',
        ];

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );

        foreach ($icons as $identifier => $source) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => $source]
            );
        }

        // Add module for csv import
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'LuxletterImport',
            'lux',
            'luxletterCsvImport',
            '',
            [
                \In2code\LuxletterImport\Controller\Backend\CsvImportController::class =>
                    'import',
            ],
            [
                'access' => 'admin',
                'iconIdentifier' => 'csv-import-module',
                'labels' => 'LLL:EXT:luxletter_import/Resources/Private/Language/locallang_db.xlf',
            ]
        );
    }
);
