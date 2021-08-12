<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GimmeBook\Infrastructure\Entity\Reader;
use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface ReaderRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Reader[]
     */
    public function getAll(): array;

    /**
     * @return Reader[]
     */
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification): ?Reader;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save(Reader $reader): Reader;
}
