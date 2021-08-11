<?php

namespace App\Controller;

use App\Entity\LeaseHistory;
use App\Entity\User;
use App\Entity\Stock;
use App\Repository\LeaseHistoryRepository;
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
class LeaseHistoryController extends AbstractController
{
    /**
     * @Route("/lease-histories/{user_id}", name="lease_histories", methods={"GET"})
     */
    public function getLeaseHistories(Request $request, EntityManagerInterface $entityManager, $user_id): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneById($user_id);
        $leaseHistories = $this->getDoctrine()->getRepository(LeaseHistory::class)->findByLessee($user);
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $serializer = new Serializer([$normalizer], [$encoder]);

        return $this->json( json_decode($serializer->serialize($leaseHistories, 'json')));
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LeaseHistoryRepository $leaseHistoryRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/lease-histories", name="lease_histories_add", methods={"POST"})
     */
    public function addLeaseHistory(Request $request, EntityManagerInterface $entityManager, LeaseHistoryRepository $leaseHistoryRepository){

        try{
            $request = $this->transformJsonBody($request);



            $leaseHistory = new LeaseHistory();

            $user = $entityManager->getRepository(User::class)->findOneByEmail($request->get('lessee')['email']);

            $booksLeased = $entityManager->getRepository(User::class)->getBooksLeased($user->getId());


            if($user->getBookLimit() <= $booksLeased)
            {
                $data = [
                    'status' => 400,
                    'errors' => "you have reached your limit please return some books to lease",
                    'msg' => $e->getMessage(),
                ];
                return $this->response($data, 422);
            }


            if($user)
            {
                $leaseHistory->setLessee($user);
            }
            $stock = $entityManager->getRepository(Stock::class)->findOneByid($request->get('stock')['id']);
            if($stock)
            {
                $leaseHistory->setStock($stock);
            }
            $returnDate = new \DateTime();
            $returnDate->add(new \DateInterval('P7D'));

            $leaseHistory->setReturnDate($returnDate);
            $entityManager->persist($leaseHistory);
            $entityManager->flush();

            $data = [
                'id' => $leaseHistory->getId(),
                'status' => 200,
                'success' => "LeaseHistory added successfully",
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
     * @param LeaseHistoryRepository $leaseHistoryRepository
     * @param $id
     * @return JsonResponse
     * @Route("/lease-histories/{id}", name="lease_histories_get", methods={"GET"})
     */
    public function getLeaseHistoryData(LeaseHistoryRepository $leaseHistoryRepository, $id){
        $leaseHistory = $leaseHistoryRepository->find($id);

        if (!$leaseHistory){
            $data = [
                'status' => 404,
                'errors' => "LeaseHistory not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($leaseHistory);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LeaseHistoryRepository $leaseHistoryRepository
     * @param $id
     * @return JsonResponse
     * @Route("/lease-histories/{id}", name="lease_histories_put", methods={"PUT"})
     */
    public function updateLeaseHistory(Request $request, EntityManagerInterface $entityManager, LeaseHistoryRepository $leaseHistoryRepository, $id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try{
            $leaseHistory = $leaseHistoryRepository->find($id);

            if (!$leaseHistory){
                $data = [
                    'status' => 404,
                    'errors' => "LeaseHistory not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);
            $leaseHistory->setReturned($request->get('returned'));
            $entityManager->flush();

            $data = [
                'id' => $leaseHistory->getId(),
                'status' => 200,
                'errors' => "LeaseHistory updated successfully",
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
     * @param LeaseHistoryRepository $leaseHistoryRepository
     * @param $id
     * @return JsonResponse
     * @Route("/lease-histories/{id}", name="lease_histories_delete", methods={"DELETE"})
     */
    public function deleteLeaseHistory(EntityManagerInterface $entityManager, LeaseHistoryRepository $leaseHistoryRepository, $id){
       
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $leaseHistory = $leaseHistoryRepository->find($id);

        if (!$leaseHistory){
            $data = [
                'status' => 404,
                'errors' => "Post not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($leaseHistory);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "LeaseHistory deleted successfully",
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