<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Query;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;

class SearchBooksQuery
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function execute(int $authorId = null, int $genreId = null): array
    {
        if (!$authorId && !$genreId) {
            return [];
        }

        $sql = 'SELECT b.* FROM book b';
        if ($authorId) {
            $sql .= ' inner join book_to_author bta on b.id = bta.book_id and bta.author_id = :authorId';
        }
        if ($genreId) {
            $sql .= ' inner join book_to_genre btg on b.id = btg.book_id and btg.genre_id = :$genreId';
        }

        return $this->entityManager->getConnection()->fetchAllAssociative(
            $sql,
            [
                'authorId' => $authorId,
                'genreId' => $genreId,
            ],
            [
                'authorId' => Types::INTEGER,
                'genreId' => Types::INTEGER,
            ]
        );
    }
}
