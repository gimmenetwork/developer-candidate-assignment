<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository\Security;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GimmeBook\Infrastructure\Entity\Security\RefreshToken;
use GimmeBook\Infrastructure\Repository\RepositoryInterface;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface RefreshTokenRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(RefreshToken $refreshToken): RefreshToken;

    public function delete(RefreshToken $refreshToken): void;

    /**
     * @return RefreshToken[]
     */
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification): ?RefreshToken;
}
