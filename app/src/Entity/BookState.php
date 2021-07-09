<?php

namespace App\Entity;

use App\Repository\BookStateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookStateRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class BookState
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="bookStates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity=Reader::class, inversedBy="bookStates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reader;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $return_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
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

    public function getReader(): ?Reader
    {
        return $this->reader;
    }

    public function setReader(?Reader $reader): self
    {
        $this->reader = $reader;

        return $this;
    }

    public function getReturnDate(): ?\DateTimeInterface
    {
        return $this->return_date;
    }

    public function setReturnDate(?\DateTimeInterface $return_date): self
    {
        $this->return_date = $return_date;

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
}
