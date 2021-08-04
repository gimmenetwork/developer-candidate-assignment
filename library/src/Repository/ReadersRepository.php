<?php

namespace App\Repository;

use App\Entity\Readers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Readers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Readers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Readers[]    findAll()
 * @method Readers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReadersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Readers::class);
    }

    // /**
    //  * @return Readers[] Returns an array of Readers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Readers
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
