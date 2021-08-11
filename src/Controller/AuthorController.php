<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
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
 * Author controller.
 *
 * @Route("/api")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/authors", name="author",methods={"GET"})
     */
    public function getAuthors(Request $request): Response
    {
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $serializer = new Serializer([$normalizer], [$encoder]);


        return $this->json( json_decode($serializer->serialize($authors, 'json')));
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param AuthorRepository $authorRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/authors", name="Authors_add", methods={"POST"})
     */
    public function addAuthor(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository){

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $request = $this->transformJsonBody($request);


            $author = new Author();
            $author->setName($request->get('name'));
            $entityManager->persist($author);
            $entityManager->flush();

            $data = [
                'id' => $author->getId(),
                'status' => 200,
                'success' => "Author added successfully",
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
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return JsonResponse
     * @Route("/authors/{id}", name="authors_get", methods={"GET"})
     */
    public function getAuthor(AuthorRepository $authorRepository, $id){
        $author = $authorRepository->find($id);

        if (!$author){
            $data = [
                'status' => 404,
                'errors' => "Author not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($author);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return JsonResponse
     * @Route("/authors/{id}", name="authors_put", methods={"PUT"})
     */
    public function updateAuthor(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $author = $authorRepository->find($id);

            if (!$author){
                $data = [
                    'status' => 404,
                    'errors' => "Author not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);
            $author->setName($request->get('name'));
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Author updated successfully",
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
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return JsonResponse
     * @Route("/authors/{id}", name="Authors_delete", methods={"DELETE"})
     */
    public function deleteAuthor(EntityManagerInterface $entityManager, AuthorRepository $authorRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $author = $authorRepository->find($id);

        if (!$author){
            $data = [
                'status' => 404,
                'errors' => "Author not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($author);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Author deleted successfully",
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