<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Entity\Book;

use Doctrine\Common\Collections\Collection;
use GimmeBook\Infrastructure\Core\Contract\ArrayableInterface;
use GimmeBook\Infrastructure\Core\Helper\EntitiesToArrayMapper;

class Book implements ArrayableInterface
{
    private int $id;

    /**
     * AKA soft deleted. It could be better to not remove the book immediately from stock.
     * We may mark the book as unavailable and wait until all the books came back.
     * When book is unavailable it's not shown in the list and is not available for leasing.
     * Such feature allows us to still show the book in a Reader's account panel and keep it waiting back.
     */
    private bool $availableForLeasing = true;

    public function __construct(
        private string $title,
        private int $year,
        private int $countInStock,
        private int $totalCount,
        private Collection $authors,
        private Collection $genres,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCountInStock(): int
    {
        return $this->countInStock;
    }

    public function setAvailableForLeasing(bool $availableForLeasing): void
    {
        $this->availableForLeasing = $availableForLeasing;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function setCountInStock(int $countInStock): void
    {
        $this->countInStock = $countInStock;
    }

    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'authors' => EntitiesToArrayMapper::map($this->authors->toArray()),
            'genres' => EntitiesToArrayMapper::map($this->genres->toArray()),
            'year' => $this->year,
            'countInStock' => $this->countInStock,
            'totalCount' => $this->totalCount,
            'availableForLeasing' => $this->availableForLeasing,
        ];
    }
}
