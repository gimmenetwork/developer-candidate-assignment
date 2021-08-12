<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Entity\Lease;
use GimmeBook\Infrastructure\Entity\Reader;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class LeaseRepository implements LeaseRepositoryInterface
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder()
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getBySpecification(SpecificationInterface $specification): array
    {
        return $specification->apply($this->createQueryBuilder())
            ->getQuery()
            ->getResult();
    }

    public function getOneBySpecification(SpecificationInterface $specification): ?Lease
    {
        return $specification->apply($this->createQueryBuilder())
                ->getQuery()
                ->getResult()[0] ?? null;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('l')
            ->from(Lease::class, 'l');
    }

    /**
     * @inheritDoc
     */
    public function save(Lease $lease): Lease
    {
        $this->entityManager->persist($lease);
        $this->entityManager->flush();

        return $lease;
    }
}
