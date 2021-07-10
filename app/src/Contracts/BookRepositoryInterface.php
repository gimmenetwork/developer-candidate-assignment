<?php

namespace App\Contracts;

use App\Entity\Book;

interface BookRepositoryInterface
{
    public function filter(string $name, string $author, string $genre): array;

    public function getDistinctGenre(): array;

    public function save(string $name, string $author, string $genre): void;

    public function edit(Book $book): void;

    public function delete(Book $book): void;
}
