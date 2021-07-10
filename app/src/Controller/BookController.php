<?php

namespace App\Controller;

use App\Contracts\AuthenticationInterface;
use App\Entity\Book;
use App\Entity\Reader;
use App\Form\Type\BookType;
use App\Service\BookService;
use App\Service\ReaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController implements AuthenticationInterface
{
    public function __construct(
        private BookService $bookService,
        private ReaderService $readerService
    )
    {
    }

    #[Route('/lease/{book}/{reader}', name: 'leaseBook')]
    public function lease(Book $book, Reader $reader = NULL): Response
    {
        if ($reader) {
            try {
                $this->bookService->leaseBook($book, $reader);

                $this->addFlash('success', $book->getName() . ' is leased to ' . $reader->getName());
                return $this->redirectToRoute('homepage');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

        }

        $readers = $this->readerService->getAllReaders();

        return $this->render('lease.html.twig', array(
            'lease_limit' => ReaderService::LEASE_LIMIT,
            'book' => $book,
            'readers' => $readers
        ));
    }

    #[Route('/return/{book}', name: 'returnBook')]
    public function return(Book $book): Response
    {
        $this->bookService->returnBook($book);

        $this->addFlash('success', $book->getName() . ' is returned');
        return $this->redirectToRoute('homepage');
    }

    #[Route('/new-book', name: 'newBook')]
    public function addBook(Request $request): Response
    {
        $form = $this->createForm(BookType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->bookService->saveBook($form->getData());

                $this->addFlash('success', $form->getData()['name'] . ' is saved');
                return $this->redirectToRoute('homepage');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        $books = $this->bookService->getAllBooks();

        return $this->render('newbook.html.twig', array(
            'form' => $form->createView(),
            'books' => $books
        ));
    }

    #[Route('/edit-book/{book}', name: 'editBook')]
    public function editBook(Book $book, Request $request): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->bookService->editBook($form->getData());

                $this->addFlash('success', $form->getData()->getName() . ' is saved');
                return $this->redirectToRoute('newBook');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('editbook.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/delete-book/{book}', name: 'deleteBook')]
    public function deleteBook(Book $book, Request $request): Response
    {
        try {
            $bookName = $book->getName();
            $this->bookService->deleteBook($book);

            $this->addFlash('success', $bookName . ' is deleted');

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('newBook');
    }
}
