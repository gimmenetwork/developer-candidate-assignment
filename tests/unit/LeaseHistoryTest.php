<?php
use App\Entity\LeaseHistory;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Stock;
use PHPUnit\Framework\TestCase;

class LeaseHistoryTest extends TestCase
{
    private LeaseHistory $leaseHistory;
    private User $user;
    private Book $book;
    private Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leaseHistory = new LeaseHistory();
        $this->user = new User();
        $this->book = new Book();
        $this->stock = new Stock();

    }

    public function testStock()
    {
        $this->stock->setBook($this->book);
        $this->stock->setCount(5);

        self::assertEquals($this->stock->getAvailable() ,5);

    }

    public function testStockAfterLease()
    {
        $this->stock->setBook($this->book);
        $this->stock->setCount(5);

        $this->leaseHistory->setLessee($this->user);
        $this->leaseHistory->setStock($this->stock);
        $this->leaseHistory->setReturned(null);
        $this->stock->addLeaseHistory($this->leaseHistory);

        self::assertEquals($this->stock->getAvailable() ,4);

    }


    public function testStockAfterReturn()
    {
        $this->stock->setBook($this->book);
        $this->stock->setCount(5);

        $this->leaseHistory->setLessee($this->user);
        $this->leaseHistory->setStock($this->stock);
        $this->leaseHistory->setReturned(null);
        $this->stock->addLeaseHistory($this->leaseHistory);


        self::assertEquals($this->stock->getAvailable() ,4);


        $this->leaseHistory->setReturned(true);
        self::assertEquals($this->stock->getAvailable() ,5);

    }


}