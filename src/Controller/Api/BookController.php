<?php

namespace App\Controller\Api;

use App\Manager\BookManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/books', name: 'api_book_')]
class BookController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            BookManager::class => BookManager::class,
        ]);
    }

    #[Route(name: 'index')]
    public function index(Request $request, BookManager $manager): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $author = $request->query->get('author');
        $genre = $request->query->get('genre');

        $data = $manager->getAll($page, $limit, ['author' => $author, 'genre' => $genre]);

        return $this->json($data);
    }
}
