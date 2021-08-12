<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Book;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Entity\Book\Author;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class AuthorRepository implements AuthorRepositoryInterface
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
        return $this->createQueryBuilder()
            ->orderBy('a.name', 'ASC')
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

    public function getOneBySpecification(SpecificationInterface $specification): ?Author
    {
        return $specification->apply($this->createQueryBuilder())
            ->getQuery()
            ->getResult()[0] ?? null;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('a')
            ->from(Author::class, 'a');
    }
}
