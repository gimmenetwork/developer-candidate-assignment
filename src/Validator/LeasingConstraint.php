<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LeasingConstraint extends Constraint
{
    public string $message = 'This owner cannot lease more than {{ number }} books.';
    public int $number = 0;

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}