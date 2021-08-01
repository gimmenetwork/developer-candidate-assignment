<?php

namespace App\Manager;

use App\Entity\Genre;

final class GenreManager extends BaseManager
{
    protected string $entity = Genre::class;
}
