<?php
namespace App\Repository;

use App\Entity\Book;
use App\Entity\Reader;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Find all books leased in this moment to a reader
     *
     * @param Reader $reader
     * @return array
     */
    public function findAllBooksLeasedByReader(Reader $reader): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->where('b.reader = :reader')
            ->andWhere('b.returnDate > :returnDate')
            ->setParameter('reader', $reader)
            ->setParameter('returnDate', new DateTime())
        ;

        return $queryBuilder->getQuery()->execute();
    }
}