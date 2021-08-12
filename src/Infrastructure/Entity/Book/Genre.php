<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Entity\Book;

use Doctrine\ORM\PersistentCollection;
use GimmeBook\Infrastructure\Core\Contract\ArrayableInterface;

class Genre implements ArrayableInterface
{
    private int $id;
    private string $name;
    /**
     * @var PersistentCollection<int, Book>
     */
    private PersistentCollection $books;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'books' => $this->books,
        ];
    }
}
