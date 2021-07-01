<?php

namespace App\Controller;

use App\Factory\ReaderFactory;
use App\Repository\ReaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReaderController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ReaderRepository $readerRepository;

    public function __construct(EntityManagerInterface $entityManager, ReaderRepository $readerRepository)
    {
        $this->entityManager = $entityManager;
        $this->readerRepository = $readerRepository;
    }

    /**
     * @Route(path="/readers", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $filters = $request->query->all();

        return $this->json(
            $this->readerRepository->findAll()
        );
    }

    /**
     * @Route(path="/readers", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);

        $this->entityManager->persist(ReaderFactory::build($content));
        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    /**
     * @Route(path="/readers/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $reader = $this->readerRepository->find($id);

        //TODO update reader if exists

        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }
}
