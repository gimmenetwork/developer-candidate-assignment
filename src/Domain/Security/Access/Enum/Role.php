<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security\Access\Enum;

/**
 * Simple roles
 * @see \GimmeBook\Migration\Version20210809102426 for values
 */
class Role
{
    public const READER = 1;
    public const ADMIN = 2;
}
