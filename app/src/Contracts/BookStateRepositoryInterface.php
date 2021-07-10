<?php

namespace App\Contracts;

use App\Entity\Reader;

interface BookStateRepositoryInterface
{
    public function getCurrentLeases(Reader $reader): array;
}
