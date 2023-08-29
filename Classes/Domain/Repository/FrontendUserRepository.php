<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Domain\Repository;

use In2code\LuxletterImport\Utility\DatabaseUtility;

class FrontendUserRepository
{
    public const TABLE_NAME = 'fe_users';

    public function hardDeleteFrontendUserFromStoragePageId(int $storagePageId): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME, true);
        $queryBuilder->delete(self::TABLE_NAME)
            ->from(self::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($storagePageId)))
            ->execute();
    }

    public function bulkInsertFrontendUsers(array $frontendUsers): void
    {
        $connection = DatabaseUtility::getConnectionForTable(self::TABLE_NAME);

        // insert in chunks to allow bigger import files
        foreach (array_chunk($frontendUsers, 5000) as $frontendUserChunk) {
            $connection->bulkInsert(
                self::TABLE_NAME,
                $frontendUserChunk,
                [
                    'email',
                    'pid',
                    'usergroup',
                    'username',
                    'password'
                ]
            );
        }
    }
}
