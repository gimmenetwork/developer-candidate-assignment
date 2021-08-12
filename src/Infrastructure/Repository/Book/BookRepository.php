<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Book;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GimmeBook\Infrastructure\Entity\Book\Book;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder()
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getBySpecification(SpecificationInterface $specification): array
    {
        $queryBuilder = $specification
            ->apply($this->createQueryBuilder())
            ->orderBy('b.title', 'ASC');
        $paginator = new Paginator($queryBuilder);

        return $paginator->getQuery()->getResult();
    }

    public function getOneBySpecification(SpecificationInterface $specification): ?Book
    {
        return $specification->apply($this->createQueryBuilder())
                ->getQuery()
                ->getResult()[0] ?? null;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b');
    }

    /**
     * @inheritDoc
     */
    public function save(Book $refreshToken): Book
    {
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $bookId): void
    {
        $book = $this->entityManager->getReference(Book::class, $bookId);
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}
