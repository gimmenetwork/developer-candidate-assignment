<?php

namespace App\Controller;

use App\Factory\AuthorFactory;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AuthorRepository $authorRepository;

    public function __construct(EntityManagerInterface $entityManager, AuthorRepository $authorRepository)
    {
        $this->entityManager = $entityManager;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route(path="/authors", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        //TODO
        $filters = $request->query->all();

        return $this->json(
            $this->authorRepository->findAll()
        );
    }

    /**
     * @Route(path="/authors", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);

        $this->entityManager->persist(AuthorFactory::build($content));
        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    /**
     * @Route(path="/authors/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $author = $this->authorRepository->find($id);

        //TODO edit author

        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    /**
     * @Route(path="/authors/{id}", methods={"PUT"})
     */
    public function delete(int $id): Response
    {
        $author = $this->authorRepository->find($id);

        if ($author) {
            throw new \Exception('author does not exist');
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }
}
