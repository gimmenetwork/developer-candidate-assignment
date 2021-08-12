<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Book\Book;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ByAvailableForLease implements SpecificationInterface
{
    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        return $queryBuilder->andWhere($alias . '.availableForLeasing = 1');
    }
}
