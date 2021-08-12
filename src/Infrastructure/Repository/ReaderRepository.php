<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Entity\Reader;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class ReaderRepository implements ReaderRepositoryInterface
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
            ->getQuery()
            ->getResult();
    }

    public function getBySpecification(SpecificationInterface $specification): array
    {
        return $specification->apply($this->createQueryBuilder())
            ->getQuery()
            ->getResult();
    }

    public function getOneBySpecification(SpecificationInterface $specification): ?Reader
    {
        return $specification->apply($this->createQueryBuilder())
                ->getQuery()
                ->getResult()[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function save(Reader $reader): Reader
    {
        $this->entityManager->persist($reader);
        $this->entityManager->flush();

        return $reader;
    }


    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('r')
            ->from(Reader::class, 'r');
    }
}
