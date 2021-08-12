<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Reader;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ByLogin implements SpecificationInterface
{
    private string $login;

    public function __construct(string $login)
    {
        $this->login = $login;
    }

    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        return $queryBuilder
            ->andWhere($queryBuilder->expr()->eq($alias . '.login', "'$this->login'"))
            ->setMaxResults(1);
    }
}
