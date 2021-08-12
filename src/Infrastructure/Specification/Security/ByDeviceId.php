<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification\Security;

use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ByDeviceId implements SpecificationInterface
{
    public function __construct(private string $deviceId)
    {
    }

    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        return $queryBuilder->andWhere($queryBuilder->expr()->eq($alias . '.deviceId', "'$this->deviceId'"));
    }
}
