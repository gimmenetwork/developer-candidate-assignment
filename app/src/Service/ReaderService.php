<?php

namespace App\Service;

use App\Repository\ReaderRepository;

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
     * @param array $readerData
     * @throws \Exception
     */
    public function saveReader(array $readerData): void
    {
        $isSet = $this->readerRepository->findOneBy(['name'=>$readerData['name']]);
        if($isSet){
            throw new \Exception('Duplicate reader name');
        }
        $this->readerRepository->save($readerData['name']);
    }

}
