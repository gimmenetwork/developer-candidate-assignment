<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Common;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ByPagination implements SpecificationInterface
{
    public function __construct(private int $page, private int $perPage)
    {
    }

    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->setMaxResults($this->perPage)
            ->setFirstResult(($this->page - 1) * $this->perPage);
    }
}
