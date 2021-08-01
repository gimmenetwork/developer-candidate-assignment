<?php

namespace App\Repository;

use App\Entity\Reader;
use App\Service\PaginatorService;
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

    /**
     * @param int $page
     * @param int $size
     * @return array
     * @throws \Exception
     */
    public function getPaginatedData(int $page = 1, int $size = 10): array
    {
        $qb = $this->createQueryBuilder('r')->orderBy('r.id', 'DESC');

        return PaginatorService::create($qb, $page, $size)->createPaginator();
    }
}
