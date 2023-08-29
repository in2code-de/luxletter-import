<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Controller\Backend;

use In2code\LuxletterImport\Domain\Repository\FrontendUserGroupRepository;
use In2code\LuxletterImport\Service\ImportService;
use In2code\LuxletterImport\Utility\FileUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CsvImportController extends ActionController
{
    protected ?ImportService $importService = null;
    protected ?FrontendUserGroupRepository  $frontendUserGroupRepository = null;

    public function injectImportService(ImportService $importService): void
    {
        $this->importService = $importService;
    }

    public function injectFrontendUserGroupRepository(FrontendUserGroupRepository $frontendUserGroupRepository): void
    {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    public function importAction(array $import = [])
    {
        $this->view->assignMultiple([
            'newsletterGroups' => $this->frontendUserGroupRepository->findLuxletterGroups(),
        ]);

        if (!empty($import)) {
            $file = $import['importFile'] ?? $this->throwErrorMessage('noFile');
            $storagePid = (int)($import['storagePid'] ?? $this->throwErrorMessage('noStoragePid'));
            $newsletterGroupUid = (int)($import['newsletterGroup'] ?? $this->throwErrorMessage('noluxletterGroup'));

            if ($this->isFileValid($file)) {
                $this->importService->importNewsletterReceiver($file, $storagePid, $newsletterGroupUid);
                $this->throwSuccessMessage();
            } else {
                $this->throwErrorMessage('noFile');
            }
        }
    }

    protected function throwSuccessMessage(): void
    {
        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:luxletter_import/Resources/Private/Language/locallang_db.xlf:csvimport.success.import'
            )
        );
    }

    protected function throwErrorMessage(string $key): void
    {
        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:luxletter_import/Resources/Private/Language/locallang_db.xlf:csvimport.error.' . $key
            ),
            '',
            AbstractMessage::ERROR
        );
    }

    protected function isFileValid(array $file): bool
    {
        if (FileUtility::isFileSelected($file) &&
            FileUtility::checkFileSize($file) &&
            FileUtility::isFileExtensionAllowed($file) &&
            !FileUtility::hasFileErrors($file)
        ) {
            return true;
        }

        return false;
    }
}
