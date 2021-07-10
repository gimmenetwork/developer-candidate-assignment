<?php

namespace App\Repository;

use App\Contracts\BookRepositoryInterface;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository implements BookRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function filter(string $name, string $author, string $genre): array
    {
        $query = $this->createQueryBuilder('b');

        if ($name != "") {
            $query->where('b.name LIKE :name');
            $query->setParameter('name', '%' . $name . '%');
        }
        if ($author != "") {
            $query->andWhere('b.author LIKE :author');
            $query->setParameter('author', '%' . $author . '%');
        }
        if ($genre != "") {
            $query->andWhere('b.genre = :genre');
            $query->setParameter('genre', $genre);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function getDistinctGenre(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.genre')
            ->groupBy('b.genre')
            ->getQuery()
            ->getResult();
    }

    public function save(string $name, string $author, string $genre): void
    {
        $newBook = new Book();
        $newBook->setName($name);
        $newBook->setAuthor($author);
        $newBook->setGenre($genre);
        $this->getEntityManager()->persist($newBook);
        $this->getEntityManager()->flush();
    }

    public function edit(Book $book): void
    {
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();
    }

    public function delete(Book $book): void
    {
        $book->setTaken(NULL);
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();

        $book->getBookStates()->clear();
        $this->getEntityManager()->remove($book);
        $this->getEntityManager()->flush();
    }
}
