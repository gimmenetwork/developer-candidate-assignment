<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Book;
use App\Repository\StockRepository;
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
 * Role controller.
 *
 * @Route("/api")
 */
class StockController extends AbstractController
{
    /**
     * @Route("/stocks", name="stocks", methods={"GET"})
     */
    public function getStocks(Request $request): Response
    {
        $stocks = $this->getDoctrine()->getRepository(Stock::class)->findAll();
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $serializer = new Serializer([$normalizer], [$encoder]);


        return $this->json( json_decode($serializer->serialize($stocks, 'json')));
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param StockRepository $stockRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/stocks", name="stocks_add", methods={"POST"})
     */
    public function addStock(Request $request, EntityManagerInterface $entityManager, StockRepository $stockRepository){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $request = $this->transformJsonBody($request);


            $stock = new Stock();

            $book = $entityManager->getRepository(Book::class)->findOneById($request->get('book')['id']);
            if($book)
            {
                $stock->setBook($book);
            }

            $stock->setCount($request->get('count'));
            $entityManager->persist($stock);
            $entityManager->flush();

            $data = [
                'id'    => $stock->getId(),
                'status' => 200,
                'success' => "Stock added successfully",
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
     * @param StockRepository $stockRepository
     * @param $id
     * @return JsonResponse
     * @Route("/stocks/{id}", name="stocks_get", methods={"GET"})
     */
    public function getStockData(StockRepository $stockRepository, $id){
        $stock = $stockRepository->find($id);

        if (!$stock){
            $data = [
                'status' => 404,
                'errors' => "Stock not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($stock);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param StockRepository $stockRepository
     * @param $id
     * @return JsonResponse
     * @Route("/stocks/{id}", name="stocks_put", methods={"PUT"})
     */
    public function updateStock(Request $request, EntityManagerInterface $entityManager, StockRepository $stockRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $stock = $stockRepository->find($id);

            if (!$stock){
                $data = [
                    'status' => 404,
                    'errors' => "Stock not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            $book = $entityManager->getRepository(Book::class)->findOneById($request->get('book')['id']);
            if($book)
            {
                $stock->setBook($book);
            }

            $stock->setCount($request->get('count'));
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Stock updated successfully",
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
     * @param StockRepository $stockRepository
     * @param $id
     * @return JsonResponse
     * @Route("/stocks/{id}", name="stocks_delete", methods={"DELETE"})
     */
    public function deleteStock(EntityManagerInterface $entityManager, StockRepository $stockRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $stock = $stockRepository->find($id);

        if (!$stock){
            $data = [
                'status' => 404,
                'errors' => "Post not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($stock);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Stock deleted successfully",
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