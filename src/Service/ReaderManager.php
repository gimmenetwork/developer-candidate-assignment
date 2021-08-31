<?php
namespace App\Service;

use App\Entity\Reader;
use App\Form\ReaderType;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ReaderManager extends EntityManagerHandler
{

    public function getReaders(ParamFetcherInterface $paramFetcher): PaginationInterface
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('r')
            ->from(Reader::class, 'r')
            ->orderBy('r.id', 'DESC')
        ;

        return $this->paginator->paginate($queryBuilder, $paramFetcher->get('page'));
    }

    /**
     * @param Request $request
     * @return FormInterface
     */
    public function postReader(Request $request): FormInterface
    {
        $reader = new Reader();
        return $this->postAction($request, $reader);
    }

    /**
     * @param Request $request
     * @param Reader $reader
     * @return FormInterface
     */
    public function putReader(Request $request, Reader $reader): FormInterface
    {
        return $this->putAction($request, $reader);
    }

    /**
     * @param Reader $reader
     */
    public function deleteReader(Reader $reader): void
    {
        $this->deleteAction($reader);
    }

    public function getFormType(): string
    {
        return ReaderType::class;
    }
}