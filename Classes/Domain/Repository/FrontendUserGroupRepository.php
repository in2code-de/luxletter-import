<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;

class FrontendUserGroupRepository
{
    public const TABLE_NAME = 'fe_groups';
    protected ConnectionPool $connectionPool;

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }

    public function findLuxletterGroups(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        return $queryBuilder->select('*')
            ->from(self::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('luxletter_receiver', '1'))
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
