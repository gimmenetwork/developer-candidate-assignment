<?php

namespace App\Manager;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\ReaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class BaseManager
{
    protected string $entity;
    protected EntityManagerInterface $em;
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $entityManager->getRepository($this->entity);
    }

    public function getAll(int $page = 1, int $size = 10, $parameters = []): array
    {
        return $this->repository->getPaginatedData($page, $size, $parameters);
    }

    public function findOne(int $id)
    {
        return $this->repository->find($id);
    }

    public function save($entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete($entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
}
