<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Book;

use GimmeBook\Infrastructure\Entity\Book\Genre;
use GimmeBook\Infrastructure\Repository\RepositoryInterface;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface GenreRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Genre[]
     */
    public function getAll(): array;

    /**
     * @return Genre[]
     */
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification): ?Genre;
}
