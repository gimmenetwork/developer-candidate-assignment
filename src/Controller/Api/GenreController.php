<?php
namespace App\Controller\Api;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class GenreController
 * @package App\Controller\Api
 *
 */
class GenreController extends AbstractFOSRestController
{
    /**
     * @var EntityManagerInterface|null
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Rest\Get(path="/api/genres")
     * @Rest\View(serializerGroups={"Default"})
     *
     * @param ParamFetcher $paramFetcher
     * @param PaginatorInterface $paginator
     * @return View
     */
    public function getGenres(ParamFetcher $paramFetcher, PaginatorInterface $paginator): View
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('g')
            ->from(Genre::class, 'g')
            ->orderBy('g.name', 'ASC')
        ;

        return $this->view($queryBuilder->getQuery()->execute(), Response::HTTP_OK);
    }
}