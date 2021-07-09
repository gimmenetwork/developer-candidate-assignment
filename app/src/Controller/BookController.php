<?php

namespace App\Controller;

use App\Contracts\AuthenticationInterface;
use App\Entity\Book;
use App\Entity\Reader;
use App\Service\BookService;
use App\Service\ReaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController implements AuthenticationInterface
{
    public function __construct(
        private BookService $bookService,
        private ReaderService $readerService
    ){

    }

    #[Route('/lease/{book}/{reader}', name: 'leaseBook')]
    public function lease(Book $book, Reader $reader = NULL): Response
    {
        if($reader){
            $this->bookService->leaseBook($book,$reader);

            $this->addFlash(
                'success',
                $book->getName().' is leased to '.$reader->getName()
            );
            return $this->redirectToRoute('homepage');
        }

        $readers = $this->readerService->getAllReaders();

        return $this->render ( 'lease.html.twig', array (
            //'form' => $form->createView(),
            'lease_limit' => ReaderService::LEASE_LIMIT,
            'book' => $book,
            'readers' => $readers
        ));
    }

    #[Route('/return/{book}', name: 'returnBook')]
    public function return(Book $book): Response
    {
        $this->bookService->returnBook($book);

        $this->addFlash(
            'success',
            $book->getName().' is returned'
        );
        return $this->redirectToRoute('homepage');
    }

    #[Route('/new-book', name: 'newBook')]
    public function addBook(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add('genre', TextType::class)
            ->add('add', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->saveBook($form->getData());

            $this->addFlash('success',$form->getData()['name'].' is saved');
            return $this->redirectToRoute('homepage');
        }

        return $this->render ( 'newbook.html.twig', array (
            'form' => $form->createView()
        ));
    }
}
