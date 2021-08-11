<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="stocks")
     */
    private $book;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\OneToMany(targetEntity=LeaseHistory::class, mappedBy="stock")
     */
    private $leaseHistories;

    public function __construct()
    {
        $this->leaseHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function getAvailable(): ?int
    {
        $leased = 0;
        foreach ($this->leaseHistories as $lease) {
            
            if(!$lease->getReturned())
            {
                $leased= $leased+ 1;
            }
            
        }


        return $this->count - $leased;
    }


    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return Collection|LeaseHistory[]
     */
    public function getLeaseHistories(): Collection
    {
        return $this->leaseHistories;
    }

    public function addLeaseHistory(LeaseHistory $leaseHistory): self
    {
        if (!$this->leaseHistories->contains($leaseHistory)) {
            $this->leaseHistories[] = $leaseHistory;
            $leaseHistory->setStock($this);
        }

        return $this;
    }

    public function removeLeaseHistory(LeaseHistory $leaseHistory): self
    {
        if ($this->leaseHistories->removeElement($leaseHistory)) {
            // set the owning side to null (unless already changed)
            if ($leaseHistory->getStock() === $this) {
                $leaseHistory->setStock(null);
            }
        }

        return $this;
    }
}
