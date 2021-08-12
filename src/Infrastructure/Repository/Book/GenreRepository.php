<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Book;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Entity\Book\Genre;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class GenreRepository implements GenreRepositoryInterface
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('g')
            ->from(Genre::class, 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getBySpecification(SpecificationInterface $specification): array
    {
        return $specification->apply($this->createQueryBuilder())
            ->getQuery()
            ->getResult();
    }

    public function getOneBySpecification(SpecificationInterface $specification): ?Genre
    {
        return $specification->apply($this->createQueryBuilder())
                ->getQuery()
                ->getResult()[0] ?? null;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('g')
            ->from(Genre::class, 'g');
    }
}
