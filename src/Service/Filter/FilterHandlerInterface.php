<?php

namespace App\Service\Filter;

use Doctrine\ORM\QueryBuilder;

interface FilterHandlerInterface
{
    /**
     * @desc Apply filters to the QueryBuilder
     *
     * @param QueryBuilder $queryBuilder
     * @param array $values
     * @return void
     */
    public function apply(QueryBuilder $queryBuilder, array $values = []): void;

    /**
     * @param string $className
     * @return bool
     */
    public function supports(string $className): bool;
}