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
    public static function build(Book $book, Reader $reader, string $returnAt, bool $isReturned): Lease
    {
        $lease = new Lease();
        $lease->setCreatedAt(new \DateTime());
        $lease->setBook($book);
        $lease->setReader($reader);
        $lease->setReturnAt(new \DateTime($returnAt));
        $lease->setIsReturned($isReturned);

        return $lease;
    }
}