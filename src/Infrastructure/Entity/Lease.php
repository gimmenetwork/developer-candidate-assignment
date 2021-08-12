<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Entity;

use GimmeBook\Infrastructure\Core\Contract\ArrayableInterface;
use GimmeBook\Infrastructure\Entity\Book\Book;

class Lease implements ArrayableInterface
{
    private int $id;
    private ?\DateTimeImmutable $whenReturned;

    public function __construct(
        private Book $book,
        private Reader $reader,
        private \DateTimeImmutable $whenLeased,
        private \DateTimeImmutable $whenToReturn,
    ) {
    }

    public function setWhenReturned(?\DateTimeImmutable $whenReturned): void
    {
        $this->whenReturned = $whenReturned;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function getWhenToReturn(): \DateTimeImmutable
    {
        return $this->whenToReturn;
    }

    public function toArray(): array
    {
        return [
            'book' => $this->book->toArray(),
            'reader' => $this->reader->toArray(),
            'whenReturned' => $this->whenReturned,
            'whenLeased' => $this->whenLeased,
            'whenToReturn' => $this->whenToReturn,
        ];
    }
}
