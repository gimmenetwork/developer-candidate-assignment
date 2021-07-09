<?php

namespace App\Repository;

use App\Entity\BookState;
use App\Entity\Reader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookState|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookState|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookState[]    findAll()
 * @method BookState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookState::class);
    }

    public function getCurrentLeases(Reader $reader){
        return $this->createQueryBuilder('r')
            ->where('r.reader = :readerId')
            ->andWhere('r.return_date IS NULL')
            ->setParameter('readerId',$reader->getId())
            ->getQuery()
            ->getResult();
    }


}
