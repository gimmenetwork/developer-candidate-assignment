<?php

namespace App\Entity;

use App\Entity\Traits\NameableTrait;
use App\Repository\ReaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReaderRepository::class)
 */
class Reader
{
    use NameableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=ReaderBook::class, mappedBy="reader")
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
            $readerBook->setReader($this);
        }

        return $this;
    }

    public function removeReaderBook(ReaderBook $readerBook): self
    {
        if ($this->readerBooks->removeElement($readerBook)) {
            // set the owning side to null (unless already changed)
            if ($readerBook->getReader() === $this) {
                $readerBook->setReader(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('(#%d) %s', $this->id, $this->name);
    }
}
