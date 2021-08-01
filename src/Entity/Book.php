<?php

namespace App\Entity;

use App\Entity\Traits\NameableTrait;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    use NameableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     */
    private ?Author $author;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="books")
     */
    private ?Genre $genre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $isbn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $summary;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private bool $isAvailable = true;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=ReaderBook::class, mappedBy="book")
     */
    private $readerBooks;

    public function __construct()
    {
        $this->readerBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * @return Collection|ReaderBook[]
     */
    public function getReaderBooks(): Collection
    {
        return $this->readerBooks;
    }

    public function addReaderBook(ReaderBook $readerBook): self
    {
        if (!$this->readerBooks->contains($readerBook)) {
            $this->readerBooks[] = $readerBook;
            $readerBook->setBook($this);
        }

        return $this;
    }

    public function removeReaderBook(ReaderBook $readerBook): self
    {
        if ($this->readerBooks->removeElement($readerBook)) {
            // set the owning side to null (unless already changed)
            if ($readerBook->getBook() === $this) {
                $readerBook->setBook(null);
            }
        }

        return $this;
    }
}
