<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function filter($name,$author,$genre){

        $query = $this->createQueryBuilder('b');

        if($name != ""){
            $query->where('b.name LIKE :name');
            $query->setParameter('name','%'.$name.'%');
        }
        if($author != ""){
            $query->andWhere('b.author LIKE :author');
            $query->setParameter('author','%'.$author.'%');
        }
        if($genre != ""){
            $query->andWhere('b.genre = :genre');
            $query->setParameter('genre',$genre);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function getDistinctGenre(){
        return $this->createQueryBuilder('b')
            ->select('b.genre')
            ->groupBy('b.genre')
            ->getQuery()
            ->getResult();
    }

    public function save(string $name, string $author, string $genre){
        $newbook = new Book();
        $newbook->setName($name);
        $newbook->setAuthor($author);
        $newbook->setGenre($genre);
        $this->getEntityManager()->persist($newbook);
        $this->getEntityManager()->flush();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
