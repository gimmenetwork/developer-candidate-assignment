<?php


namespace App\Factory;


use App\Entity\Book;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BookFactory
{
    private const WHITELISTED_PARAMETERS = [
        'genre',
        'title',
    ];

    public static function build(array $authors, array $params): Book
    {
        $book = new Book();
        self::addAuthors($book, $authors);
        self::fillWhitelistedValues($book, $params);

        return $book;
    }

    public static function fillWhitelistedValues(Book $book, array $params): void
    {
        foreach ($params as $key => $value) {
            if (in_array($key, self::WHITELISTED_PARAMETERS)) {
                $setter = sprintf('set%s', ucfirst($key));
                $book->$setter($value);
            }
        }
    }

    public static function addAuthors(Book $book, array $authors): void
    {
        $authorsCollection = new ArrayCollection();
        foreach ($authors as $author) {
            $authorsCollection->add($author);
        }
        $book->setAuthors($authorsCollection);
    }
}
