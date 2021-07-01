<?php

namespace App\Entity;

use App\Repository\LeaseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeaseRepository::class)
 */
class Lease
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Reader::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Reader $reader;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeInterface $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeInterface $return_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $is_returned;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Book $book;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReader(): ?Reader
    {
        return $this->reader;
    }

    public function setReader(?Reader $reader): self
    {
        $this->reader = $reader;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getReturnAt(): ?\DateTimeInterface
    {
        return $this->return_at;
    }

    public function setReturnAt(\DateTimeInterface $return_at): self
    {
        $this->return_at = $return_at;

        return $this;
    }

    public function getIsReturned(): ?bool
    {
        return $this->is_returned;
    }

    public function setIsReturned(bool $is_returned): self
    {
        $this->is_returned = $is_returned;

        return $this;
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
}
