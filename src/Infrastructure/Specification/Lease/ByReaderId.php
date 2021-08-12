<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Lease;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ByReaderId implements SpecificationInterface
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        return $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq($alias . '.reader', $this->id)
            );
    }
}
