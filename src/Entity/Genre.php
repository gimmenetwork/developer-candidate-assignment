<?php

namespace App\Entity;

use App\Entity\Traits\NameableTrait;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    use NameableTrait;

    /**
     * @Ignore()
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @Ignore()
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="genre")
     */
    private $books;

    #[Pure] public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setGenre($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getGenre() === $this) {
                $book->setGenre(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('(#%d) %s', $this->getId(), $this->getName());
    }
}
