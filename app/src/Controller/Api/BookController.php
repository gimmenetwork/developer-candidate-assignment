<?php

namespace App\Controller\Api;

use App\Service\BookService;
use App\Validators\GetBooksValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api") */
class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService
    ){
    }

    #[Route('/get-books', name: 'getBooks')]
    public function getBooks(Request $request, GetBooksValidator $validator): Response
    {
        try {
            //$validator->validate($request->toArray()); //It doesn't needed. Filter parameters are optional

            $filterData = [
                'name'=> '',
                'author'=> $request->toArray()['author'] ?? '',
                'genre'=> $request->toArray()['genre'] ?? '',
            ];

            $books =  $this->bookService->filterBooks($filterData);

            foreach ($books as $book){
                $data[]=[
                    'id' => $book->getId(),
                    'name' => $book->getName(),
                    'is_available' => !(bool)$book->getTaken()
                ];
            }

            return $this->json($data ?? []);

        } catch (\Exception $e) {
            return (new JsonResponse(["error" => $e->getMessage()], 403));
        }
    }

}
