<?php


namespace App\Tests\Factory;


use App\Entity\Author;
use App\Factory\BookFactory;
use App\Tests\BaseClass;

class BookFactoryTest extends BaseClass
{
    private static BookFactory $bookFactory;

    protected function setUp(): void
    {
        self::$bookFactory = new BookFactory();
    }

    public function test_build()
    {
        $id = 1;
        $book = self::$bookFactory::build([new Author()], [
            'genre' => 'tragedy',
            'title' => 'Othello',
            'id' => $id
        ]);

        //confirm that only whitelisted parameters can be set
        self::assertFalse($book->getId() == $id);
    }
}
