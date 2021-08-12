<?php

declare(strict_types=1);

namespace GimmeBook\Application\Controller\Api;

use GimmeBook\Infrastructure\Query\SearchBooksQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController
{
    public function __construct(private SearchBooksQuery $searchQuery)
    {
    }

    public function search(Request $request): JsonResponse
    {
        $authorId = $request->query->getInt('author');
        $genreId = $request->query->getInt('genre');

        $books = $this->searchQuery->execute($authorId, $genreId);

        return new JsonResponse(['books' => $books]);
    }
}
