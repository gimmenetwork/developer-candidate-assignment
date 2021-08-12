<?php

declare(strict_types=1);

namespace GimmeBook\Application\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GimmeBook\Application\Service\RedirectToIndexResponseProducer;
use GimmeBook\Infrastructure\Entity\Lease;
use GimmeBook\Infrastructure\Repository\Book\BookRepositoryInterface;
use GimmeBook\Infrastructure\Repository\LeaseRepositoryInterface;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Common\ById;
use GimmeBook\Infrastructure\Specification\CompoundSpecification;
use GimmeBook\Infrastructure\Specification\Lease\ByBookId;
use GimmeBook\Infrastructure\Specification\Lease\ByReaderId;
use GimmeBook\Infrastructure\Specification\Lease\NotReturned;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see \Application\Controller\LeaseControllerTest
 */
class LeaseController
{
    private const MAX_BOOKS_PER_READER = 3;

    public function __construct(
        private RedirectToIndexResponseProducer $redirectToIndex,
        private LeaseRepositoryInterface $leaseRepository,
        private BookRepositoryInterface $bookRepository,
        private ReaderRepositoryInterface $readerRepository,
    ) {
    }

    public function leaseBook(Request $request, int $bookId): Response
    {
        $readerId = $request->attributes->getInt('readerId');
        if ($readerId <= 0 || $bookId <= 0) {
            return $this->redirectToIndex->produce('You can\'t lease the book', false);
        }

        // check if user has 3 books
        $notReturnedCount = count(
            $this->leaseRepository->getBySpecification(
                new CompoundSpecification(
                    new ByReaderId($readerId),
                    new NotReturned(),
                )
            )
        );
        if ($notReturnedCount >= self::MAX_BOOKS_PER_READER) {
            return $this->redirectToIndex->produce('You need to return some book to lease this one', false);
        }

        // check if no book in stock
        $book = $this->bookRepository->getOneBySpecification(new ById($bookId));
        if (!$book || $book->getCountInStock() <= 0) {
            return $this->redirectToIndex->produce('This book cannot be leased, try later please', false);
        }

        $reader = $this->readerRepository->getOneBySpecification(new ById($readerId));
        if (!$reader) {
            return $this->redirectToIndex->produce('No such reader exists', false);
        }

        // finally, lease
        $book->setCountInStock($book->getCountInStock() - 1);
        $whenToReturn = (new \DateTimeImmutable())->add(new \DateInterval('P5D'));
        $lease = new Lease($book, $reader, new \DateTimeImmutable(), $whenToReturn);
        try {
            $this->bookRepository->save($book);
            $this->leaseRepository->save($lease);
        } catch (OptimisticLockException | ORMException $e) {
            return $this->redirectToIndex->produce('Something went wrong while leasing, please try again', false);
        }

        return $this->redirectToIndex->produce('Congrats! Enjoy your reading', true);
    }

    public function returnBook(Request $request, int $bookId): Response
    {
        $readerId = $request->attributes->getInt('readerId');
        if ($readerId <= 0 || $bookId <= 0) {
            return $this->redirectToIndex->produce('You can\'t return this book', false);
        }

        // check if no book in stock
        $book = $this->bookRepository->getOneBySpecification(new ById($bookId));
        if (!$book) {
            return $this->redirectToIndex->produce('This book cannot be returned, try later please', false);
        }

        // finally, return
        $book->setCountInStock($book->getCountInStock() + 1);
        $lease = $this->leaseRepository->getOneBySpecification(
            new CompoundSpecification(
                new ByReaderId($readerId),
                new ByBookId($bookId),
            )
        );
        if (!$lease) {
            return $this->redirectToIndex->produce('This book is not leased', false);
        }

        $lease->setWhenReturned(new \DateTimeImmutable());

        try {
            $this->bookRepository->save($book);
            $this->leaseRepository->save($lease);
        } catch (OptimisticLockException | ORMException $e) {
            return $this->redirectToIndex->produce('Something went wrong while returning, please try again', false);
        }

        return $this->redirectToIndex->produce('Thank you!', true);
    }
}
