<?php

namespace App\Validator;

use App\Entity\Reader;
use App\Entity\ReaderBook;
use App\Repository\ReaderBookRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class LeaseBookValidator extends ConstraintValidator
{
    private ReaderBookRepository $repository;
    private int $limit;

    public function __construct(ReaderBookRepository $repository, int $limit)
    {
        $this->repository = $repository;
        $this->limit = $limit;
    }

    /**
     * @param Reader $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!($value instanceof Reader)) {
            throw new UnexpectedValueException($value, Reader::class);
        }

        if ($this->repository->getTotalBook($value) >= $this->limit) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%maximum_leasable_books%', $this->limit)
                ->addViolation();
        }
    }
}
