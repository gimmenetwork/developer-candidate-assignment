<?php

namespace App\Repository;

use App\Entity\Book;
use App\Exception\InvalidFilterParameterException;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function getPaginatedData(int $page = 1, int $limit = PaginatorService::LIMIT, array $parameters =  []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b, a, g')
            ->innerJoin('b.author', 'a')
            ->innerJoin('b.genre', 'g')
            ->orderBy('b.id', 'DESC')
        ;

        foreach ($parameters as $key => $value) {
            $this->checkParameters($key);

            if (empty($value)) {
                continue;
            }

            $qb->andWhere($qb->expr()->like(sprintf('LOWER(%s.name)', $key[0]), ':'.$key))
                ->setParameter($key, '%'.mb_strtolower($value).'%');
        }

        return PaginatorService::create($qb, $page, $limit)->createPaginator();
    }

    /**
     * @param string $key
     * @throws InvalidFilterParameterException
     */
    private function checkParameters(string $key): void
    {
        if (!in_array($key, ['author', 'genre'])) {
            throw new InvalidFilterParameterException($key);
        }
    }
}
