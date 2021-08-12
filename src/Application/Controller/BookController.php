<?php

declare(strict_types=1);

namespace GimmeBook\Application\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GimmeBook\Application\Service\RedirectToIndexResponseProducer;
use GimmeBook\Infrastructure\Entity\Book\Book;
use GimmeBook\Infrastructure\Repository\Book\AuthorRepositoryInterface;
use GimmeBook\Infrastructure\Repository\Book\BookRepositoryInterface;
use GimmeBook\Infrastructure\Repository\Book\GenreRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Common\ById;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private GenreRepositoryInterface $genreRepository,
        private BookRepositoryInterface $bookRepository,
        private RedirectToIndexResponseProducer $redirectToIndex,
    ) {
    }

    private function doCommonValidation(string $title, int $year, int $total): ?Response
    {
        if (!$title || $year <= 0 || $total <= 0) {
            return $this->redirectToIndex->produce('Please, fill all the book data', false);
        }

        return null;
    }

    private function saveBook(Book $book, string $message): Response
    {
        try {
            $this->bookRepository->save($book);
        } catch (OptimisticLockException | ORMException $e) {
            return $this->redirectToIndex->produce('Error while saving the book, try again', false);
        }

        return $this->redirectToIndex->produce($message, true);
    }

    public function add(Request $request): Response
    {
        $title = $request->request->get('title');
        $year = $request->request->getInt('year');
        $total = $request->request->getInt('total');

        if ($failResponse = $this->doCommonValidation($title, $year, $total)) {
            return $failResponse;
        }

        $authorId = $request->request->getInt('author');
        $genreId = $request->request->getInt('genre');

        $author = $this->authorRepository->getOneBySpecification(new ById($authorId));
        if (!$author) {
            return $this->redirectToIndex->produce('No such author', false);
        }

        $genre = $this->genreRepository->getOneBySpecification(new ById($genreId));
        if (!$genre) {
            return $this->redirectToIndex->produce('No such genre', false);
        }

        $book = new Book(
            $title,
            $year,
            $total,
            $total,
            new ArrayCollection([$author]),
            new ArrayCollection([$genre]),
        );

        return $this->saveBook($book, "The book \"$title\" was added");
    }

    public function edit(Request $request, int $bookId): Response
    {
        if ($bookId <= 0) {
            return $this->redirectToIndex->produce('No such book', false);
        }

        $title = $request->request->get('title');
        $year = $request->request->getInt('year');
        $total = $request->request->getInt('total');
        $countInStock = $request->request->getInt('count');
        $isAvailable = $request->request->getBoolean('available');

        if ($countInStock <= 0 || $total <= 0 || $countInStock > $total) {
            return $this->redirectToIndex->produce('Wrong stock counts', false);
        }

        if ($failResponse = $this->doCommonValidation($title, $year, $total)) {
            return $failResponse;
        }

        $book = $this->bookRepository->getOneBySpecification(new ById($bookId));
        if (!$book) {
            return $this->redirectToIndex->produce('Book is not found', false);
        }

        $book->setTitle($title);
        $book->setYear($year);
        $book->setCountInStock($countInStock);
        $book->setTotalCount($total);
        $book->setAvailableForLeasing($isAvailable);

        return $this->saveBook($book, "The book \"$title\" was edited");
    }

    public function remove(int $bookId): Response
    {
        if ($bookId <= 0) {
            return $this->redirectToIndex->produce('No such book', false);
        }

        try {
            $this->bookRepository->delete($bookId);
        } catch (OptimisticLockException | ORMException $e) {
            return $this->redirectToIndex->produce('Error while deleting the book, try again', false);
        }

        return $this->redirectToIndex->produce('Book was deleted', true);
    }
}
