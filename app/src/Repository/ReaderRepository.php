<?php

namespace App\Repository;

use App\Contracts\ReaderRepositoryInterface;
use App\Entity\Reader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reader|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reader|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reader[]    findAll()
 * @method Reader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReaderRepository extends ServiceEntityRepository implements ReaderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reader::class);
    }

    public function filter(string $name): array
    {
        $query = $this->createQueryBuilder('r');

        if ($name != "") {
            $query->where('r.name LIKE :name');
            $query->setParameter('name', '%' . $name . '%');
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function save(string $name): void
    {
        $newReader = new Reader();
        $newReader->setName($name);
        $this->getEntityManager()->persist($newReader);
        $this->getEntityManager()->flush();
    }

    public function edit(Reader $reader): void
    {
        $this->getEntityManager()->persist($reader);
        $this->getEntityManager()->flush();
    }
}
