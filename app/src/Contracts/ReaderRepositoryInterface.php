<?php

namespace App\Contracts;

use App\Entity\Reader;

interface ReaderRepositoryInterface
{
    public function filter(string $name): array;

    public function save(string $name): void;

    public function edit(Reader $reader): void;
}
