<?php

namespace App\Service;

use App\Repository\BookStateRepository;
use App\Repository\ReaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReaderService
{
    CONST LEASE_LIMIT = 3;

    public function __construct(
        private ReaderRepository $readerRepository
    )
    {
    }

    /**
     * @return array
     */
    public function getAllReaders(): array
    {
        return $this->readerRepository->findAll();
    }

    /**
     * @return array
     */
    public function saveReader(array $readerData)
    {
        $this->readerRepository->save($readerData['name']);
    }

}
