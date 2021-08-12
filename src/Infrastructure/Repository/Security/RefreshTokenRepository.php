<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use GimmeBook\Infrastructure\Entity\Security\RefreshToken;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function getBySpecification(SpecificationInterface $specification): array
    {
        return $specification->apply($this->createQueryBuilder())
            ->getQuery()
            ->getResult();
    }

    public function getOneBySpecification(SpecificationInterface $specification): ?RefreshToken
    {
        return $specification->apply($this->createQueryBuilder())
                ->getQuery()
                ->getResult()[0] ?? null;
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('rt')
            ->from(RefreshToken::class, 'rt');
    }

    /**
     * @inheritDoc
     */
    public function save(RefreshToken $refreshToken): RefreshToken
    {
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }

    public function delete(RefreshToken $refreshToken): void
    {
        $this->entityManager->remove($refreshToken);
        $this->entityManager->flush();
    }
}
