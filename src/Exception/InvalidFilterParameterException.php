<?php

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class InvalidFilterParameterException extends InvalidArgumentException
{
    #[Pure] public function __construct(string $value)
    {
        parent::__construct(sprintf('The "%s" is not valid parameter, please use valid parameter key.', $value), 0, null);
    }
}
