<?php

namespace App\Entity;

use App\Repository\ReaderBookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ReaderBookRepository::class)
 * @UniqueEntity(fields={"reader", "book"}, message="The reader already has this book.")
 */
class ReaderBook
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Reader::class, inversedBy="readerBooks")
     */
    private ?Reader $reader;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="readerBooks")
     */
    private ?Book $book;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?\DateTimeImmutable $returnAt;

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

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getReturnAt(): ?\DateTimeImmutable
    {
        return $this->returnAt;
    }

    public function setReturnAt(\DateTimeImmutable $returnAt): self
    {
        $this->returnAt = $returnAt;

        return $this;
    }
}
