<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Controller\Backend;

use In2code\LuxletterImport\Domain\Repository\FrontendUserGroupRepository;
use In2code\LuxletterImport\Service\ImportService;
use In2code\LuxletterImport\Utility\FileUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\UploadedFile;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

use function pathinfo;

use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_OK;

class CsvImportController extends ActionController
{
    protected ?ImportService $importService = null;
    protected ?FrontendUserGroupRepository $frontendUserGroupRepository = null;
    protected ?ModuleTemplateFactory $moduleTemplateFactory = null;

    public function injectImportService(ImportService $importService): void
    {
        $this->importService = $importService;
    }

    public function injectFrontendUserGroupRepository(FrontendUserGroupRepository $frontendUserGroupRepository): void
    {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function importAction(array $import = []): ResponseInterface
    {
        if (!empty($import)) {
            $this->handleImport($import);
        }

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assign(
            'newsletterGroups',
            $this->frontendUserGroupRepository->findLuxletterGroups(),
        );
        return $moduleTemplate->renderResponse('CsvImport/Import');
    }

    protected function handleImport(array $import): void
    {
        $truncate = (bool) ($import['truncate'] ?? false);
        $files = $this->request->getUploadedFiles();
        if (!isset($files['import']['importFile']) || !$files['import']['importFile'] instanceof UploadedFile) {
            $this->addErrorMessage('noFile');
            return;
        }
        /** @var UploadedFile $file */
        $file = $files['import']['importFile'];
        if (!$this->isFileValid($file)) {
            $this->addErrorMessage('noFile');
        }

        if (!isset($import['storagePid'])) {
            $this->addErrorMessage('noStoragePid');
            return;
        }
        $storagePid = (int) $import['storagePid'];

        if (!isset($import['newsletterGroup'])) {
            $this->addErrorMessage('noluxletterGroup');
            return;
        }
        $newsletterGroupUid = (int) $import['newsletterGroup'];

        $this->importService->importNewsletterReceiver($file, $storagePid, $newsletterGroupUid, $truncate);
        $this->addSuccessMessage();
    }

    protected function addSuccessMessage(): void
    {
        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:luxletter_import/Resources/Private/Language/locallang_db.xlf:csvimport.success.import',
            ),
        );
    }

    protected function addErrorMessage(string $key): void
    {
        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:luxletter_import/Resources/Private/Language/locallang_db.xlf:csvimport.error.' . $key,
            ),
            '',
            ContextualFeedbackSeverity::ERROR,
        );
    }

    protected function isFileValid(UploadedFile $file): bool
    {
        return $file->getError() === UPLOAD_ERR_OK
            && $file->getSize() > 0
            && pathinfo($file->getClientFilename(), PATHINFO_EXTENSION) === 'csv';
    }
}
