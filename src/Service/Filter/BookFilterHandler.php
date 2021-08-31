<?php

namespace App\Service\Filter;

use App\Entity\Book;
use Doctrine\ORM\QueryBuilder;

/**
 * Handles the filter process of the book listing
 *
 * Class BookFilterHandler
 * @package App\Service\Filter
 */
class BookFilterHandler implements FilterHandlerInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array $values
     */
    public function apply(QueryBuilder $queryBuilder, array $values = []): void
    {
        if (isset($values['name'])) {
            $queryBuilder->where($queryBuilder->expr()->like('b.name', ':name'));
            $queryBuilder->setParameter('name', '%' . $values['name'] . '%');
        }

        if (isset($values['genre'])) {
            $expr = $queryBuilder->expr()->like('genre.name', ':genre');
            if (isset($values['name'])) {
                $queryBuilder->andWhere($expr);
            } else {
                $queryBuilder->where($expr);
            }
            $queryBuilder->setParameter('genre', '%' . $values['genre'] . '%');
        }
    }

    public function supports(string $className): bool
    {
        return $className === Book::class;
    }
}