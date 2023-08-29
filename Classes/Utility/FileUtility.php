<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class FileUtility
{
    public const NO_FILE_SELECTED = 4;

    public static function isFileSelected(array $file): bool
    {
        return (int)($file['error'] ?? 0) !== self::NO_FILE_SELECTED;
    }

    public static function hasFileErrors(array $file): bool
    {
        return ($file['error'] ?? 1) > 0;
    }

    public static function checkFileSize(array $file): bool
    {
        return ($file['size'] ?? 0) > 0;
    }

    public static function isFileExtensionAllowed(array $file): bool
    {
        $allowedExtensions = GeneralUtility::trimExplode(
            ',',
            'csv',
            true
        );
        return in_array(self::getFileExtension($file['name'] ?? ''), $allowedExtensions);
    }

    public static function getFileExtension(string $fileName): string
    {
        $information = pathinfo($fileName);
        if (!empty($information['extension'])) {
            return strtolower($information['extension']);
        }
        return '';
    }
}
