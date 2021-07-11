<?php

namespace App\Controller\Api;

use App\Contracts\ApiAuthenticationInterface;
use App\Dto\Response\ApiResponse;
use App\Entity\Book;
use App\Entity\Reader;
use App\Service\ReaderService;
use App\Validators\GetReadersValidator;
use App\Validators\ReaderValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api") */
class ReaderController extends AbstractController implements ApiAuthenticationInterface
{
    public function __construct(
        private ReaderService $readerService
    )
    {
    }

    #[Route('/get-readers', name: 'api-getReaders', methods: 'POST')]
    public function getReaders(Request $request, GetReadersValidator $validator): Response
    {
        try {
            //$validator->validate($request->toArray()); //It doesn't needed. Filter parameters are optional

            $filterData = [
                'name' => $request->toArray()['name'] ?? ''
            ];

            $readers = $this->readerService->filterReaders($filterData);

            foreach ($readers as $reader) {
                $leaseRecords = [];
                foreach ($reader->getBookStates() as $lease) {
                    $leaseRecords[] = [
                        'id' => $lease->getId(),
                        'book_name'=>$lease->getBook()->getName(),
                        'return_date'=>$lease->getReturnDate()?->format("Y-m-d H:i:s"),
                        'created_at'=>$lease->getCreatedAt()->format("Y-m-d H:i:s")
                    ];
                }

                $data[] = [
                    'id' => $reader->getId(),
                    'name' => $reader->getName(),
                    'lease_records' => $leaseRecords
                ];
            }

            return (new ApiResponse(json_encode($data ?? []), Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/add-reader', name: 'api-addReader', methods: 'POST')]
    public function addReader(Request $request, ReaderValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());

            $this->readerService->saveReader($request->toArray());

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/edit-reader/{reader}', name: 'api-editReader', methods: 'PUT')]
    public function editReader(Reader $reader, Request $request, ReaderValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());

            $reader->setName($request->toArray()['name']);
            $this->readerService->editReader($reader);

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }


}
