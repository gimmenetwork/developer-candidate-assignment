<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Lease;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class NotReturned implements SpecificationInterface
{
    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        return $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->isNull($alias . '.whenReturned')
            );
    }
}
