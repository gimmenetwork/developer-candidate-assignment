<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity=BookState::class, mappedBy="book", orphanRemoval=true)
     */
    private $bookStates;

    /**
     * @ORM\OneToOne(targetEntity=BookState::class, cascade={"persist", "remove"})
     */
    private $taken;

    public function __construct()
    {
        $this->bookStates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection|BookState[]
     */
    public function getBookStates(): Collection
    {
        return $this->bookStates;
    }

    public function addBookState(BookState $bookState): self
    {
        if (!$this->bookStates->contains($bookState)) {
            $this->bookStates[] = $bookState;
            $bookState->setBook($this);
        }

        return $this;
    }

    public function removeBookState(BookState $bookState): self
    {
        if ($this->bookStates->removeElement($bookState)) {
            // set the owning side to null (unless already changed)
            if ($bookState->getBook() === $this) {
                $bookState->setBook(null);
            }
        }

        return $this;
    }

    public function getTaken(): ?BookState
    {
        return $this->taken;
    }

    public function setTaken(?BookState $taken): self
    {
        $this->taken = $taken;

        return $this;
    }
}
