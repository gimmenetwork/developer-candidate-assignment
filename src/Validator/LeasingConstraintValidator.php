<?php

namespace App\Validator;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class LeasingConstraintValidator extends ConstraintValidator
{

    protected EntityManagerInterface $entityManager;

    /**
     * LeasingConstraintValidator constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Book $value
     * @param LeasingConstraint|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $reader = $value->getReader();

        if ($reader) {
            /** @var BookRepository $repository */
            $repository = $this->entityManager->getRepository(Book::class);
            $leasedBooks = $repository->findAllBooksLeasedByReader($reader);

            if (count($leasedBooks) >= $constraint->number) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ number }}', $constraint->number)
                    ->atPath('reader')
                    ->addViolation();
            }
        }
    }
}