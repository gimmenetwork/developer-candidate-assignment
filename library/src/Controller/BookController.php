<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="book_list")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/books/create", name="book_add")
     */
    public function create(): Response
    {
        return $this->render('book/create.html.twig');
    }

    /**
     * @Route("/books", name="book_insert", methods={"POST"})
     */
    public function insert(): Response
    {

        // return $this->redirectToRoute('book_list');
    }

    /**
     * @Route("/books/{id}", name="book_show")
     */
    public function show(Book $book): Response
    {
        return $this->json($book);
    }
}
