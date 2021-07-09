<?php

namespace App\Repository;

use App\Entity\Reader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reader|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reader|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reader[]    findAll()
 * @method Reader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reader::class);
    }

    public function save(string $name){
        $newReader = new Reader();
        $newReader->setName($name);
        $this->getEntityManager()->persist($newReader);
        $this->getEntityManager()->flush();
    }
}
