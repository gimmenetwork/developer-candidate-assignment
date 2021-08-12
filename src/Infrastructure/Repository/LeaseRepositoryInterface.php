<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GimmeBook\Infrastructure\Entity\Lease;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface LeaseRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Lease[]
     */
    public function getAll(): array;

    /**
     * @return Lease[]
     */
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification): ?Lease;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Lease $lease): Lease;
}
