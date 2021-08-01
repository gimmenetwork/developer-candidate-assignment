<?php

namespace App\Repository;

use App\Entity\Author;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param int $page
     * @param int $size
     * @return array
     * @throws \Exception
     */
    public function getPaginatedData(int $page = 1, int $size = 10): array
    {
        $qb = $this->createQueryBuilder('a')->orderBy('a.id', 'DESC');

        return PaginatorService::create($qb, $page, $size)->createPaginator();
    }
}
