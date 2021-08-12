<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;

interface SpecificationInterface
{
    public function apply(QueryBuilder $queryBuilder): QueryBuilder;
}
