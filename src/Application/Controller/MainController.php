<?php

declare(strict_types=1);

namespace GimmeBook\Application\Controller;

use GimmeBook\Infrastructure\Core\Helper\EntitiesToArrayMapper;
use GimmeBook\Domain\Security\Access\Enum\Role;
use GimmeBook\Infrastructure\Entity\Book\Book;
use GimmeBook\Infrastructure\Repository\Book\BookRepositoryInterface;
use GimmeBook\Infrastructure\Repository\LeaseRepositoryInterface;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Book\Book\ByAvailableForLease;
use GimmeBook\Infrastructure\Specification\Common\ByPagination;
use GimmeBook\Infrastructure\Specification\CompoundSpecification;
use GimmeBook\Infrastructure\Specification\Common\ById;
use GimmeBook\Infrastructure\Specification\Lease\ByReaderId;
use GimmeBook\Infrastructure\Specification\Lease\NotReturned;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MainController
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private ReaderRepositoryInterface $readerRepository,
        private LeaseRepositoryInterface $leaseRepository,
        private Environment $twig,
    ) {
    }

    /**
     * Homepage
     */
    public function index(Request $request, int $page): Response
    {
        $perPage = 10;
        $specifications = [new ByPagination($page ?: 1, $perPage)];
        if ($readerId = $request->attributes->getInt('readerId')) {
            // authorized reader is here, check role
            $reader = $this->readerRepository->getOneBySpecification(new ById($readerId));
            if (!$reader || $reader->getRoleId() !== Role::ADMIN) {
                // show only available book for regular readers
                $specifications[] = new ByAvailableForLease();
            }
        }

        $availableBooks = $this->bookRepository->getBySpecification(
            new CompoundSpecification(...$specifications)
        );
        $booksList = EntitiesToArrayMapper::map($availableBooks);

        $leasedBooks = [];
        if (isset($reader)) {
            // for an authorized reader add info about leasing
            $leasedBooks = $this->leaseRepository->getBySpecification(
                new CompoundSpecification(
                    new ByReaderId($reader->getId()),
                    new NotReturned(),
                )
            );

            foreach ($leasedBooks as $lease) {
                foreach ($booksList as &$bookArr) {
                    if ($lease->getBook()->getId() !== $bookArr['id']) {
                        continue;
                    }

                    $bookArr['whenToReturn'] = $lease->getWhenToReturn();
                }
                unset($bookArr);
            }

            $leasedBooks = EntitiesToArrayMapper::map($leasedBooks);
        }

        $booksCount = count($this->bookRepository->getAll());

        $data = [
            'booksList' => $booksList,
            'pages' => (int)ceil($booksCount / $perPage),
            'currentPage' => $page,
            'leasedBooks' => $leasedBooks,
        ];

        return new Response($this->twig->render('mainPage/booksList.twig', $data));
    }

    /**
     * Account page
     */
    public function account(): Response
    {
        return new Response();
    }

    public function notFound(): Response
    {
        return new Response('not found', Response::HTTP_NOT_FOUND);
    }
}
