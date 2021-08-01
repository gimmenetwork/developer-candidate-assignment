<?php

namespace App\DataTransformer;

use App\Entity\Book;
use App\Manager\BookManager;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BookIdTransformer implements DataTransformerInterface
{
    private BookManager $manager;

    public function __construct(BookManager $bookManager)
    {
        $this->manager = $bookManager;
    }

    /**
     * @param Book|null $book
     * @return int|string
     */
    public function transform($book): int|string
    {
        if (null === $book) {
            return '';
        }

        return $book->getId();
    }

    public function reverseTransform($bookId): ?Book
    {
        if (!$bookId) {
            return null;
        }

        $book = $this->manager->findOne($bookId);

        if (null === $book) {
            $privateErrorMessage = 'A book does not exist!';
            $publicErrorMessage = 'The given "{{ value }}" value is not a valid book id.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $bookId,
            ]);

            throw $failure;
        }

        return $book;
    }
}
