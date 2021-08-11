<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
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
 * Genre controller.
 *
 * @Route("/api")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/genres", name="genres", methods={"GET"})
     */
    public function getGenres(Request $request): Response
    {
        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $serializer = new Serializer([$normalizer], [$encoder]);


        return $this->json( json_decode($serializer->serialize($genres, 'json')));
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param GenreRepository $genreRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/genres", name="genres_add", methods={"POST"})
     */
    public function addGenre(Request $request, EntityManagerInterface $entityManager, GenreRepository $genreRepository){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $request = $this->transformJsonBody($request);


            $genre = new Genre();
            $genre->setName($request->get('name'));
            $entityManager->persist($genre);
            $entityManager->flush();

            $data = [
                'id' => $genre->getId(),
                'status' => 200,
                'success' => "Genre added successfully",
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
     * @param GenreRepository $genreRepository
     * @param $id
     * @return JsonResponse
     * @Route("/genres/{id}", name="genres_get", methods={"GET"})
     */
    public function getGenre(GenreRepository $genreRepository, $id){
        $genre = $genreRepository->find($id);

        if (!$genre){
            $data = [
                'status' => 404,
                'errors' => "Genre not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($genre);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param GenreRepository $genreRepository
     * @param $id
     * @return JsonResponse
     * @Route("/genres/{id}", name="genres_put", methods={"PUT"})
     */
    public function updateGenre(Request $request, EntityManagerInterface $entityManager, GenreRepository $genreRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $genre = $genreRepository->find($id);

            if (!$genre){
                $data = [
                    'status' => 404,
                    'errors' => "Genre not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name') || !$request->request->get('description')){
                throw new \Exception();
            }

            $genre->setName($request->get('name'));
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Genre updated successfully",
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
     * @param GenreRepository $genreRepository
     * @param $id
     * @return JsonResponse
     * @Route("/genres/{id}", name="genres_delete", methods={"DELETE"})
     */
    public function deleteGenre(EntityManagerInterface $entityManager, GenreRepository $genreRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $genre = $genreRepository->find($id);

        if (!$genre){
            $data = [
                'status' => 404,
                'errors' => "Genre not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($genre);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Genre deleted successfully",
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