<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;

use function array_chunk;

class FrontendUserRepository
{
    public const TABLE_NAME = 'fe_users';
    protected ConnectionPool $connectionPool;

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }

    public function hardDeleteFrontendUserFromStoragePageId(int $storagePageId): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->delete(self::TABLE_NAME)
            ->from(self::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($storagePageId)))
            ->executeStatement();
    }

    public function bulkInsertFrontendUsers(array $frontendUsers): void
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_NAME);

        // insert in chunks to allow bigger import files
        foreach (array_chunk($frontendUsers, 5000) as $frontendUserChunk) {
            $connection->bulkInsert(
                self::TABLE_NAME,
                $frontendUserChunk,
                [
                    'email',
                    'pid',
                    'crdate',
                    'usergroup',
                    'username',
                    'password',
                ],
            );
        }
    }
}
