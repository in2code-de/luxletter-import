<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Service;

use In2code\LuxletterImport\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportService
{
    protected ?FrontendUserRepository $frontendUserRepository = null;

    public function __construct(?FrontendUserRepository $frontendUserRepository = null)
    {
        $this->frontendUserRepository = $frontendUserRepository ?? GeneralUtility::makeInstance(FrontendUserRepository::class);
    }

    public function importNewsletterReceiver(
        array $importFile,
        int $newsletterReceiverStoragePid,
        int $newsletterGroup
    ): void {
        $this->frontendUserRepository->hardDeleteFrontendUserFromStoragePageId($newsletterReceiverStoragePid);
        $receiverMails = $this->parseCsvFileToArray($importFile);
        $enrichedReceiverMails = $this->addFieldsToReceiver(
            $receiverMails,
            $newsletterReceiverStoragePid,
            $newsletterGroup
        );

        $this->frontendUserRepository->bulkInsertFrontendUsers($enrichedReceiverMails);
    }

    protected function parseCsvFileToArray(array $csvFile): array
    {
        $rawFileArray = file($csvFile['tmp_name']);
        $encodedFileArray = mb_convert_encoding($rawFileArray, 'UTF-8', 'ISO-8859-1');
        return array_map('trim', $encodedFileArray);
    }

    protected function addFieldsToReceiver(
        array $receiverMails,
        int $newsletterReceiverStoragePid,
        int $newsletterGroup
    ): array {
        foreach ($receiverMails as $index => $receiverMail) {
            $receiverMails[$index] = [
                $receiverMail,
                $newsletterReceiverStoragePid,
                $newsletterGroup,
                $receiverMail,
                '$2y$12$An3DZFDgAPet8/GKa/PzROxJdl/wA8Gl44iCTAl2FvAXj1VMfI/4.'
            ];
        }

        return $receiverMails;
    }
}
