<?php
namespace App\Controller\Api;

use App\Entity\Book;
use App\Service\BookManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class BookController
 * @package App\Controller\Api
 *
 */
class BookController extends AbstractFOSRestController
{
    private BookManager $bookManager;

    public function __construct( BookManager $bookManager)
    {
        $this->bookManager = $bookManager;
    }

    /**
     * @Rest\Get(path="/api/books")
     * @Rest\View(serializerGroups={"Default"})
     * @QueryParam(name="page", requirements="\d+", default="1", description="The page number")
     * @QueryParam(name="name", default="", description="The name of the book")
     * @QueryParam(name="genre", default="", description="The genre of the book")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return View
     */
    public function getBooks(ParamFetcherInterface $paramFetcher): View
    {
        $pagination = $this->bookManager->getBooks($paramFetcher);

        return $this->view($pagination, Response::HTTP_OK);
    }


    /**
     * @Rest\Get(path="/api/books/{id}")
     * @Rest\View(serializerGroups={"Default"})
     *
     * @param Book $book
     * @return View
     */
    public function getBook(Book $book): View
    {
        return $this->view($book);
    }

    /**
     * Creates a Book resource
     *
     * @Rest\Post(path="/api/books")
     * @param Request $request
     * @return View
     */
    public function postBook(Request $request): View
    {
        $form = $this->bookManager->postBook($request);

        if (false === $form->isValid()) {
            return $this->view($form->getErrors(true), Response::HTTP_BAD_REQUEST);
        }

        return $this->routeRedirectView(
            'app_api_book_getbook',
            ['id' => $form->getData()->getId()],
            Response::HTTP_CREATED
        );
    }

    /**
     * Updates a Book resource
     *
     * @Rest\Put(path="/api/books/{id}")
     * @param Request $request
     * @param Book $book
     * @return View
     */
    public function putBook(Request $request, Book $book): View
    {
        $form = $this->bookManager->putBook($request, $book);

        if (false === $form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        return $this->routeRedirectView(
            'app_api_book_getbook',
            ['id' => $form->getData()->getId()],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Deletes a Book resource
     *
     * @Rest\Delete(path="/api/books/{id}")
     * @param Book $book
     * @return View
     */
    public function deleteBook(Book $book): View
    {
        $this->bookManager->deleteBook($book);
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Updates a Book resource by leasing to a reader
     *
     * @Rest\Patch(path="/api/books/lease/{id}")
     * @param Request $request
     * @param Book $book
     * @return View
     */
    public function leaseBook(Request $request, Book $book): View
    {
        $form = $this->bookManager->leaseBook($request, $book);

        if (false === $form->isValid()) {
            return $this->view($form->getErrors(true), Response::HTTP_BAD_REQUEST);
        }

        return $this->routeRedirectView(
            'app_api_book_getbook',
            ['id' => $form->getData()->getId()],
            Response::HTTP_NO_CONTENT
        );
    }
}