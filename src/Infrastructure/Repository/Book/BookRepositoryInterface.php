<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Book;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GimmeBook\Infrastructure\Entity\Book\Book;
use GimmeBook\Infrastructure\Repository\RepositoryInterface;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface BookRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Book[]
     */
    public function getAll(): array;

    /**
     * @return Book[]
     */
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification): ?Book;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Book $refreshToken): Book;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(int $bookId): void;
}
