<?php

namespace App\Service;

use App\Contracts\ReaderRepositoryInterface;
use App\Entity\Reader;

class ReaderService
{
    CONST LEASE_LIMIT = 3;

    public function __construct(
        private ReaderRepositoryInterface $readerRepository
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
     * @param array $filterData
     * @return array
     */
    public function filterReaders(array $filterData): array
    {
        return $this->readerRepository->filter($filterData['name'] ?? "");
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

    /**
     * @param \App\Entity\Reader $reader
     * @throws \Exception
     */
    public function editReader(Reader $reader): void
    {
        if($reader->getName() ==""){
            throw new \Exception('Invalid Edit Data');
        }

        $this->readerRepository->edit($reader);
    }

}
