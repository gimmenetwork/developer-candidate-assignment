<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Genre;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Book controller.
 *
 * @Route("/api")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books", methods={"GET"})
     */
    public function getBooks(Request $request): Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $serializer = new Serializer([$normalizer], [$encoder]);


        return $this->json( json_decode($serializer->serialize($books, 'json')));
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/books", name="books_add", methods={"POST"})
     */
    public function addBook(Request $request, EntityManagerInterface $entityManager, BookRepository $bookRepository){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $request = $this->transformJsonBody($request);


            $book = new Book();
            $book->setName($request->get('name'));
            $author = $entityManager->getRepository(Author::class)->findOneById($request->get('author')['id']);
            if($author)
            {
                $book->setAuthor($author);
            }
            
            foreach ($request->get('genres') as  $value) {
                $genre = $entityManager->getRepository(Genre::class)->findOneById($value['id']);
                if($genre)
                {
                    $book->addGenre($genre);
                }
            }
            $entityManager->persist($book);
            $entityManager->flush();

            $data = [
                'id'=> $book->getId(),
                'status' => 200,
                'success' => "Book added successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * @param BookRepository $bookRepository
     * @param $id
     * @return JsonResponse
     * @Route("/books/{id}", name="books_get", methods={"GET"})
     */
    public function getBook(BookRepository $bookRepository, $id){
        $book = $bookRepository->find($id);

        if (!$book){
            $data = [
                'status' => 404,
                'errors' => "Book not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($book);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @param $id
     * @return JsonResponse
     * @Route("/books/{id}", name="books_put", methods={"PUT"})
     */
    public function updateBook(Request $request, EntityManagerInterface $entityManager, BookRepository $bookRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $book = $bookRepository->find($id);

            if (!$book){
                $data = [
                    'status' => 404,
                    'errors' => "Book not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);
            $genres = array();
      
            $book->setName($request->get('name'));

            if($request->get('author'))
            {
                $author = $entityManager->getRepository(Author::class)->findOneById($request->get('author')['id']);
                if($author)
                {
                    $book->setAuthor($author);
                }
            }
            
            if($request->get('genres'))
            {
                foreach ($request->get('genres') as  $value) {
                    $genres[] = $value['id'];
                    $genre = $entityManager->getRepository(Genre::class)->findOneById($value['id']);
                    if($genre)
                    {
                        $book->addGenre($genre);
                    }
                }

                foreach($book->getGenres() as $genre)
                {
                    if(!in_array( $genre->getId(),$genres))
                    {
                        $book->removeGenre($genre);
                    }
                }
            }
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Book updated successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
                'msg' => $e->getMessage(),
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * @param BookRepository $bookRepository
     * @param $id
     * @return JsonResponse
     * @Route("/books/{id}", name="books_delete", methods={"DELETE"})
     */
    public function deleteBook(EntityManagerInterface $entityManager, BookRepository $bookRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $book = $bookRepository->find($id);

        if (!$book){
            $data = [
                'status' => 404,
                'errors' => "Book not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($book);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Book deleted successfully",
        ];
        return $this->response($data);
    }


    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

}
