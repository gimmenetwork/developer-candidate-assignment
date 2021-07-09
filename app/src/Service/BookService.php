<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookState;
use App\Entity\Reader;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @return array
     */
    public function getAllBooks(): array
    {
        return $this->bookRepository->findAll();
    }

    /**
     * @return array
     */
    public function filterBooks(array $filterData): array
    {
        return $this->bookRepository->filter($filterData['name'], $filterData['author'], $filterData['genre']);
    }

    /**
     * @return array
     */
    public function getAvailableBooks(): array
    {
        return $this->bookRepository->findBy(['taken' => NULL]);
    }

    /**
     * @return array
     */
    public function leaseBook(Book $book, Reader $reader)
    {
        $bookState = new BookState();
        $bookState->setBook($book);
        $bookState->setReader($reader);
        $this->em->persist($bookState);

        $book->setTaken($bookState);
        $this->em->persist($book);
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function returnBook(Book $book)
    {
        $book->getTaken()->setReturnDate(new \DateTime());
        $book->setTaken(null);
        $this->em->persist($book);
        $this->em->flush();
    }

    /**
     * @return array
     */
    public function saveBook(array $bookData)
    {
        $this->bookRepository->save($bookData['name'], $bookData['author'], $bookData['genre']);
    }

    /**
     * @return array
     */
    public function getGenreList(): array
    {
        $genres = $this->bookRepository->getDistinctGenre();
        foreach ($genres as $genre) {
            $data[$genre['genre']] = $genre['genre'];
        }
        return $data ?? [];
    }

}
