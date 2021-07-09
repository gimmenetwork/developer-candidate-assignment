<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookState;
use App\Entity\Reader;
use App\Repository\BookRepository;
use App\Repository\BookStateRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookStateRepository $bookStateRepository,
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
     * @param array $filterData
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
     * @param \App\Entity\Book $book
     * @param \App\Entity\Reader $reader
     * @throws \Exception
     */
    public function leaseBook(Book $book, Reader $reader): void
    {
        $currentLeaseCount = count($this->bookStateRepository->getCurrentLeases($reader));

        if($currentLeaseCount >= ReaderService::LEASE_LIMIT){
            throw new \Exception('User leased already '.ReaderService::LEASE_LIMIT. 'books');
        }

        $bookState = new BookState();
        $bookState->setBook($book);
        $bookState->setReader($reader);
        $this->em->persist($bookState);

        $book->setTaken($bookState);
        $this->em->persist($book);
        $this->em->flush();
    }

    /**
     * @param \App\Entity\Book $book
     */
    public function returnBook(Book $book): void
    {
        $book->getTaken()->setReturnDate(new \DateTime());
        $book->setTaken(null);
        $this->em->persist($book);
        $this->em->flush();
    }

    /**
     * @param array $bookData
     * @throws \Exception
     */
    public function saveBook(array $bookData): void
    {
        $isSet = $this->bookRepository->findOneBy(['name'=>$bookData['name']]);
        if($isSet){
            throw new \Exception("Duplicate Book name");
        }

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
