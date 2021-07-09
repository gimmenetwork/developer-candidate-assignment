<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

class GetBooksValidator
{
    private $validator;
    private $constraints;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
        $this->setConstraints();
    }

    public function setConstraints()
    {
        $this->constraints = new Collection([
            'author' => [],
            'genre' => []
        ]);
    }

    public function validate($value = null)
    {
        $result = $this->validator->validate($value, $this->constraints);
        foreach ($result as $fieldName => $violation) {
            throw new \Exception($violation->getPropertyPath()." ".$violation->getMessage());
        }
    }
}
