<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Service;

use In2code\LuxletterImport\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Http\UploadedFile;

use function array_map;
use function file;
use function time;

class ImportService
{
    public function __construct(
        protected FrontendUserRepository $frontendUserRepository,
    ) {}

    public function importNewsletterReceiver(
        UploadedFile $file,
        int $newsletterReceiverStoragePid,
        int $newsletterGroup,
        bool $truncate,
    ): void {
        if ($truncate) {
            $this->frontendUserRepository->hardDeleteFrontendUserFromStoragePageId($newsletterReceiverStoragePid);
        }

        $receiverMails = $this->parseCsvFileToArray($file);
        $enrichedReceiverMails = $this->addFieldsToReceiver(
            $receiverMails,
            $newsletterReceiverStoragePid,
            $newsletterGroup,
        );

        $this->frontendUserRepository->bulkInsertFrontendUsers($enrichedReceiverMails);
    }

    protected function parseCsvFileToArray(UploadedFile $file): array
    {
        $rawFileArray = file($file->getTemporaryFileName());
        $encodedFileArray = mb_convert_encoding($rawFileArray, 'UTF-8', 'ISO-8859-1');
        return array_map('trim', $encodedFileArray);
    }

    protected function addFieldsToReceiver(
        array $receiverMails,
        int $newsletterReceiverStoragePid,
        int $newsletterGroup,
    ): array {
        foreach ($receiverMails as $index => $receiverMail) {
            $receiverMails[$index] = [
                $receiverMail,
                $newsletterReceiverStoragePid,
                time(),
                $newsletterGroup,
                $receiverMail,
                '$2y$12$An3DZFDgAPet8/GKa/PzROxJdl/wA8Gl44iCTAl2FvAXj1VMfI/4.',
            ];
        }

        return $receiverMails;
    }
}
