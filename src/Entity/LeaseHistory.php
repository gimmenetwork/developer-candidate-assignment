<?php

namespace App\Entity;

use App\Repository\LeaseHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeaseHistoryRepository::class)
 */
class LeaseHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Stock::class, inversedBy="leaseHistories")
     */
    private $stock;

    /**
     * @ORM\Column(type="date")
     */
    private $returnDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $returned;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="leaseHistories")
     */
    private $lessee;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getReturnDate()
    {
        return $this->returnDate->format('Y-m-d');
    }

    public function setReturnDate(\DateTimeInterface $returnDate): self
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    public function getReturned(): ?bool
    {
        return $this->returned;
    }

    public function setReturned(?bool $returned): self
    {
        $this->returned = $returned;

        return $this;
    }

    public function getLessee(): ?User
    {
        return $this->lessee;
    }

    public function setLessee(?User $lessee): self
    {
        $this->lessee = $lessee;

        return $this;
    }
}
