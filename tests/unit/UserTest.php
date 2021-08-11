<?php
use App\Entity\LeaseHistory;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Stock;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
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

    public function testUserLimit()
    {
        $this->user->setBookLimit(3);

        self::assertEquals($this->user->getRemainingLimit() ,3);
    }


    public function testUserLimitAfterLease()
    {
        $this->user->setBookLimit(3);

        $this->stock->setBook($this->book);
        $this->stock->setCount(5);

        $this->leaseHistory->setLessee($this->user);
        $this->leaseHistory->setStock($this->stock);
        $this->leaseHistory->setReturned(null);
        $this->user->addLeaseHistory($this->leaseHistory);



        self::assertEquals($this->user->getRemainingLimit() ,2);
    }


    public function testUserLimitAfterReturn()
    {
        $this->user->setBookLimit(3);

        $this->stock->setBook($this->book);
        $this->stock->setCount(5);

        $this->leaseHistory->setLessee($this->user);
        $this->leaseHistory->setStock($this->stock);
        $this->leaseHistory->setReturned(null);
        $this->user->addLeaseHistory($this->leaseHistory);
        self::assertEquals($this->user->getRemainingLimit() ,2);

        $this->leaseHistory->setReturned(true);


        self::assertEquals($this->user->getRemainingLimit() ,3);


    }




}