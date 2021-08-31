<?php
namespace App\Controller\Api;

use App\Entity\Reader;
use App\Service\ReaderManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ReaderController
 * @package App\Controller\Api
 *
 */
class ReaderController extends AbstractFOSRestController
{
    /**
     * @var ReaderManager|null
     */
    private ReaderManager $readerManager;

    public function __construct(ReaderManager $readerManager)
    {
        $this->readerManager = $readerManager;
    }

    /**
     * Get the Readers collection
     *
     * @Rest\Get(path="/api/readers")
     * @Rest\View(serializerGroups={"Default"})
     * @QueryParam(name="page", requirements="\d+", default="1", description="The page number")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return View
     */
    public function getReaders(ParamFetcherInterface $paramFetcher): View
    {
        $pagination = $this->readerManager->getReaders($paramFetcher);

        return $this->view($pagination, Response::HTTP_OK);
    }


    /**
     * Get a Reader resource
     *
     * @Rest\Get(path="/api/readers/{id}")
     * @Rest\View(serializerGroups={"Default"})
     *
     * @param Reader $reader
     * @return View
     */
    public function getReader(Reader $reader): View
    {
        return $this->view($reader);
    }

    /**
     * Creates a Reader resource
     *
     * @Rest\Post(path="/api/readers")
     * @param Request $request
     * @return View
     */
    public function postReader(Request $request): View
    {
        $form = $this->readerManager->postReader($request);

        if (false === $form->isValid()) {
            return $this->view($form->getErrors(true), Response::HTTP_BAD_REQUEST);
        }

        return $this->routeRedirectView(
            'app_api_reader_getreader',
            ['id' => $form->getData()->getId()],
            Response::HTTP_CREATED
        );
    }

    /**
     * Updates a Reader resource
     *
     * @Rest\Put(path="/api/readers/{id}")
     * @param Request $request
     * @param Reader $reader
     * @return View
     */
    public function putReader(Request $request, Reader $reader): View
    {
        $form = $this->readerManager->putReader($request, $reader);

        if (false === $form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        return $this->routeRedirectView(
            'app_api_reader_getreader',
            ['id' => $form->getData()->getId()],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Deletes a Reader resource
     *
     * @Rest\Delete(path="/api/readers/{id}")
     * @param Reader $reader
     * @return View
     */
    public function deleteReader(Reader $reader): View
    {
        $this->readerManager->deleteReader($reader);
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}