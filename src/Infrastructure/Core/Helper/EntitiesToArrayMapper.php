<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Core\Helper;

use GimmeBook\Infrastructure\Core\Contract\ArrayableInterface;

class EntitiesToArrayMapper
{
    /**
     * @return ArrayableInterface[]
     */
    public static function map(array $entities): array
    {
        return array_map(static fn ($entity): array => $entity->toArray(), $entities);
    }
}
