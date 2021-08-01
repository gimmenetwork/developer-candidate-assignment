<?php

namespace App\Manager;

use App\Entity\Author;

final class AuthorManager extends BaseManager
{
    protected string $entity = Author::class;
}
