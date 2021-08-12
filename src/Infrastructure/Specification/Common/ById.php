<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Common;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ById implements SpecificationInterface
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
            ->andWhere($queryBuilder->expr()->eq($alias . '.id', $this->id))
            ->setMaxResults(1);
    }
}
