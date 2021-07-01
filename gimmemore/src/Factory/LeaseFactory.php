<?php


namespace App\Factory;


use App\Entity\Book;
use App\Entity\Reader;
use App\Entity\Lease;

class LeaseFactory
{
    /**
     * @throws \Exception
     */
    public static function build(Book $book, Reader $reader, string $returnAt, bool $isReturned = false): Lease
    {
        $lease = new Lease();
        $lease->setCreatedAt(new \DateTimeImmutable());
        $lease->setBook($book);
        $lease->setReader($reader);
        $lease->setReturnAt(new \DateTimeImmutable($returnAt));
        $lease->setIsReturned($isReturned);

        return $lease;
    }
}