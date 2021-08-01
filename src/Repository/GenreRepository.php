<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    /**
     * @param int $page
     * @param int $size
     * @return array
     * @throws \Exception
     */
    public function getPaginatedData(int $page = 1, int $size = 10): array
    {
        $qb = $this->createQueryBuilder('g')->orderBy('g.id', 'DESC');

        return PaginatorService::create($qb, $page, $size)->createPaginator();
    }
}
