<?php

namespace App\Contracts;

use App\Entity\Reader;

interface ReaderRepositoryInterface
{
    public function save(string $name): void;

    public function edit(Reader $reader): void;
}
