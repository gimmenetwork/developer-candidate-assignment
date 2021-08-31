<?php
namespace App\Entity;

use App\Repository\BookRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @CustomAssert\LeasingConstraint(number=3)
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private ?string $author = null;

    /**
     * @ORM\ManyToOne(targetEntity="Genre")
     * @ORM\JoinColumn(name="genre_id", referencedColumnName="id")
     */
    private ?Genre $genre = null;

    /**
     * @ORM\ManyToOne(targetEntity="Reader")
     * @ORM\JoinColumn(name="reader_id", referencedColumnName="id")
     */
    private ?Reader $reader = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="return_date", type="datetime", nullable=true)
     */
    private ?DateTime $returnDate = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string $author
     * @return $this
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Genre|null
     */
    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    /**
     * @param Genre|null $genre
     * @return Book
     */
    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Reader|null
     */
    public function getReader(): ?Reader
    {
        return $this->reader;
    }

    /**
     * @param Reader|null $reader
     * @return Book
     */
    public function setReader(?Reader $reader): self
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getReturnDate(): ?DateTime
    {
        return $this->returnDate;
    }

    /**
     * @param DateTime|null $returnDate
     * @return $this
     */
    public function setReturnDate(?DateTime $returnDate): self
    {
        $this->returnDate = $returnDate;
        return $this;
    }

    public function getIsAvailable(): bool
    {
        if ($this->returnDate instanceof DateTime) {
            return $this->returnDate <= new DateTime();
        }
        return true;
    }
}