<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }



    /**
     * @Route("/book/{id}", name="book_show")
     */
    public function show(Book $book, SerializerInterface $serializer): Response
    {
        $b = $serializer->serialize($book, 'json');
        return new Response($b);

    }
}
