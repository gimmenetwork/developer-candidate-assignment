<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Core\Contract;

interface ArrayableInterface
{
    public function toArray(): array;
}
