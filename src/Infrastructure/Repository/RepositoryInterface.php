<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Repository;

use GimmeBook\Infrastructure\Specification\SpecificationInterface;

interface RepositoryInterface
{
    public function getBySpecification(SpecificationInterface $specification): array;

    public function getOneBySpecification(SpecificationInterface $specification);
}
