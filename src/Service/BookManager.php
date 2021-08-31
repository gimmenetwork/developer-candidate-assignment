<?php
namespace App\Service;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\LeaseBookType;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class BookManager extends EntityManagerHandler
{

    public function getBooks(ParamFetcherInterface $paramFetcher): PaginationInterface
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('b')
            ->from(Book::class, 'b')
            ->join('b.genre', 'genre')
            ->orderBy('b.id', 'DESC')
        ;

        /**
         * This is the call where the filters are applied to the query builder
         * The filters are sent from the frontend through the GET collection request
         * The frontend filter component supports multiple operators ( contains, equal, start with, end with, is empty, is not empty )
         * The filter system I made supports only the contains operator, therefor, the where clause is LIKE '%value%'
         */
        $this->filterManager->applyFilters($queryBuilder, $paramFetcher);

        return $this->paginator->paginate($queryBuilder, $paramFetcher->get('page'));
    }

    /**
     * @param Request $request
     * @return FormInterface
     */
    public function postBook(Request $request): FormInterface
    {
        $book = new Book();
        return $this->postAction($request, $book);
    }

    /**
     * @param Request $request
     * @param Book $book
     * @return FormInterface
     */
    public function putBook(Request $request, Book $book): FormInterface
    {
        return $this->putAction($request, $book);
    }

    /**
     * @param Book $book
     */
    public function deleteBook(Book $book): void
    {
        $this->deleteAction($book);
    }

    /**
     * @param Request $request
     * @param Book $book
     * @return FormInterface
     */
    public function leaseBook(Request $request, Book $book): FormInterface
    {
        $content = json_decode($request->getContent(), true);
        $form = $this->formFactory->create(LeaseBookType::class, $book);
        $form->submit($content, false);

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $form;
    }

    public function getFormType(): string
    {
        return BookType::class;
    }
}