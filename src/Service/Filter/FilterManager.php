<?php

namespace App\Service\Filter;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;

/**
 * This class handles the filter event
 * We can have different type of filters or different type of entities who needs to be filtered
 * So, the filter parameters are different and each filter handler should belongs to one single entity
 *
 * Class FilterManager
 * @package App\Service\Filter
 */
class FilterManager
{
    private iterable $filterHandlers;

    public function __construct(iterable $filterHandlers)
    {
        $this->filterHandlers = $filterHandlers;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param ParamFetcherInterface $paramFetcher
     */
    public function applyFilters(QueryBuilder $queryBuilder, ParamFetcherInterface $paramFetcher): void
    {
        $className = $this->getClassName($queryBuilder);

        /** @var FilterHandlerInterface $filterHandler */
        foreach ($this->filterHandlers as $filterHandler)
        {
            if ($filterHandler->supports($className)) {
                $values = $paramFetcher->all();
                unset($values['page']);

                $newValues = array_filter($values, function ($value) {
                    return (bool) $value;
                });

                $filterHandler->apply($queryBuilder, $newValues);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return string|null
     */
    private function getClassName(QueryBuilder $queryBuilder): ?string
    {
        $rootEntities = $queryBuilder->getRootEntities();

        return $rootEntities[0] ?? null;
    }
}