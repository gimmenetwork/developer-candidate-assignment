<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Book;

use GimmeBook\Infrastructure\Entity\Book\Author;
use GimmeBook\Infrastructure\Repository\RepositoryInterface;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface AuthorRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Author[]
     */
    public function getAll(): array;

    /**
     * @return Author[]
     */
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification): ?Author;
}
