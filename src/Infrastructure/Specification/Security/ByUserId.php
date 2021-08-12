<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Security;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ByUserId implements SpecificationInterface
{
    public function __construct(private int $userId)
    {
    }

    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        return $queryBuilder->andWhere($queryBuilder->expr()->eq($alias . '.userId', $this->userId));
    }
}
