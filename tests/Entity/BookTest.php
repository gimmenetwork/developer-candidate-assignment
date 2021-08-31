<?php
namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Genre;
use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    protected Book $entity;

    public function setUp(): void
    {
        $this->entity = new Book();
    }

    public function testNull(): void
    {
        $this->assertNull($this->entity->getId());
        $this->assertNull($this->entity->getName());
        $this->assertNull($this->entity->getAuthor());
        $this->assertNull($this->entity->getGenre());
        $this->assertNull($this->entity->getReturnDate());
    }

    public function testTrue(): void
    {
        $this->assertTrue($this->entity->getIsAvailable());
    }

    public function testFalse(): void
    {
        $returnDate = new DateTime();
        $interval = new DateInterval('P1M');
        $returnDate->add($interval);
        $this->entity->setReturnDate($returnDate);
        $this->assertFalse($this->entity->getIsAvailable());
    }

    public function testSettersAndGetters(): void
    {
        $this->entity->setName('TestName');
        $this->assertEquals('TestName', $this->entity->getName());

        $this->entity->setAuthor('TestAuthor');
        $this->assertEquals('TestAuthor', $this->entity->getAuthor());

        $this->entity->setGenre(new Genre());
        $this->assertInstanceOf(Genre::class, $this->entity->getGenre());

        $this->entity->setReturnDate(new DateTime());
        $this->assertInstanceOf(DateTime::class, $this->entity->getReturnDate());
    }
}