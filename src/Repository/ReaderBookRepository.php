<?php

namespace App\Repository;

use App\Entity\Reader;
use App\Entity\ReaderBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReaderBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReaderBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReaderBook[]    findAll()
 * @method ReaderBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReaderBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReaderBook::class);
    }

    public function getTotalBook(Reader $reader)
    {
        try {
            return $this->createQueryBuilder('rb')
                ->select('count(rb.id)')
                ->where('rb.reader = :reader')
                ->setParameter('reader', $reader)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException)  {
            return 0;
        }
    }
}
