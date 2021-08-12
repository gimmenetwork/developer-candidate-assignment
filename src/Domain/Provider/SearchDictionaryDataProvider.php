<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Provider;

use GimmeBook\Infrastructure\Entity\Book\Author;
use GimmeBook\Infrastructure\Entity\Book\Genre;
use GimmeBook\Infrastructure\Repository\Book\AuthorRepositoryInterface;
use GimmeBook\Infrastructure\Repository\Book\GenreRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Provides the common data for header for response
 */
class SearchDictionaryDataProvider
{
    private AuthorRepositoryInterface $authorRepository;
    private GenreRepositoryInterface $genreRepository;

    public function __construct(AuthorRepositoryInterface $authorRepository, GenreRepositoryInterface $genreRepository)
    {
        $this->authorRepository = $authorRepository;
        $this->genreRepository = $genreRepository;
    }

    #[ArrayShape(['authors' => "array", 'genres' => "array"])]
    public function getData(): array
    {
        return [
            'authors' => array_map(
                static fn (Author $author): array => $author->toArray(),
                $this->authorRepository->getAll(),
            ),
            'genres' => array_map(
                static fn (Genre $genre): array => $genre->toArray(),
                $this->genreRepository->getAll(),
            ),
        ];
    }
}
