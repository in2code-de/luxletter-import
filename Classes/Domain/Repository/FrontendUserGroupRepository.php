<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\Domain\Repository;

use In2code\LuxletterImport\Utility\DatabaseUtility;

class FrontendUserGroupRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
{
    public const TABLE_NAME = 'fe_groups';

    public function findLuxletterGroups(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        return $queryBuilder->select('*')
            ->from(self::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('luxletter_receiver', '1'))
            ->execute()
            ->fetchAllAssociative();
    }
}
