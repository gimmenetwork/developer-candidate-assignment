<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\ReaderBook;

final class BookManager extends BaseManager
{
    protected string $entity = Book::class;

    public function lease(ReaderBook $entity): void
    {
        $book = $entity->getBook();
        $book->setIsAvailable(false);

        $this->em->persist($book);
        $this->em->persist($entity);
        $this->em->flush();
    }
}
