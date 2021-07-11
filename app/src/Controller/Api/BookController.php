<?php

namespace App\Controller\Api;

use App\Contracts\ApiAuthenticationInterface;
use App\Dto\Response\ApiResponse;
use App\Entity\Book;
use App\Entity\Reader;
use App\Service\BookService;
use App\Validators\BookValidator;
use App\Validators\GetBooksValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api") */
class BookController extends AbstractController implements ApiAuthenticationInterface
{
    public function __construct(
        private BookService $bookService
    )
    {
    }

    #[Route('/get-books', name: 'api-getBooks', methods: 'POST')]
    public function getBooks(Request $request, GetBooksValidator $validator): Response
    {
        try {
            //$validator->validate($request->toArray()); //It doesn't needed. Filter parameters are optional

            $filterData = [
                'name' => '',
                'author' => $request->toArray()['author'] ?? '',
                'genre' => $request->toArray()['genre'] ?? '',
            ];

            $books = $this->bookService->filterBooks($filterData);

            foreach ($books as $book) {
                $data[] = [
                    'id' => $book->getId(),
                    'name' => $book->getName(),
                    'is_available' => !(bool)$book->getTaken()
                ];
            }

            return (new ApiResponse(json_encode($data ?? []), Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/add-book', name: 'api-addBook', methods: 'POST')]
    public function addBook(Request $request, BookValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());

            $this->bookService->saveBook($request->toArray());

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/edit-book/{book}', name: 'api-editBook', methods: 'PUT')]
    public function editBook(Book $book, Request $request, BookValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());

            $book->setName($request->toArray()['name']);
            $book->setAuthor($request->toArray()['author']);
            $book->setGenre($request->toArray()['genre']);
            $this->bookService->editBook($book);

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/delete-book/{book}', name: 'api-deleteBook', methods: 'DELETE')]
    public function deleteBook(Book $book): Response
    {
        try {
            $this->bookService->deleteBook($book);

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/lease-book/{book}/{reader}', name: 'api-leaseBook')]
    public function leaseBook(Book $book, Reader $reader): Response
    {
        try {
            $this->bookService->leaseBook($book, $reader);

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send(); //TODO different error codes for different exceptions
        }
    }

    #[Route('/return-book/{book}', name: 'api-returnBook')]
    public function returnBook(Book $book): Response
    {
        try {
            $this->bookService->returnBook($book);

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

}
