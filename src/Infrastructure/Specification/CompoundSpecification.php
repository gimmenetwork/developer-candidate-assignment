<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;

/**
 * Allows applying multiple specifications
 */
class CompoundSpecification implements SpecificationInterface
{
    private array $specifications;

    public function __construct(SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function apply(QueryBuilder $queryBuilder): QueryBuilder
    {
        foreach ($this->specifications as $specification) {
            $queryBuilder = $specification->apply($queryBuilder);
        }

        return $queryBuilder;
    }
}
