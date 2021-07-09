<?php

namespace App\Entity;

use App\Repository\ReaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReaderRepository::class)
 */
class Reader
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
     * @ORM\OneToMany(targetEntity=BookState::class, mappedBy="reader", orphanRemoval=true)
     */
    private $bookStates;

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
            $bookState->setReader($this);
        }

        return $this;
    }

    public function removeBookState(BookState $bookState): self
    {
        if ($this->bookStates->removeElement($bookState)) {
            // set the owning side to null (unless already changed)
            if ($bookState->getReader() === $this) {
                $bookState->setReader(null);
            }
        }

        return $this;
    }
}
