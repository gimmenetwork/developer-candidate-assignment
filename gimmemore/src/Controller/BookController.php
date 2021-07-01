<?php

namespace App\Controller;

use App\Factory\BookFactory;
use App\Factory\LeaseFactory;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\LeaseRepository;
use App\Repository\ReaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private const MAX_ALLOWED_READER_LEASES = 3;

    private EntityManagerInterface $entityManager;
    private BookRepository $bookRepository;
    private LeaseRepository $leaseRepository;
    private ReaderRepository $readerRepository;
    private AuthorRepository $authorRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        ReaderRepository $readerRepository,
        LeaseRepository $leaseRepository,
        AuthorRepository $authorRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        $this->readerRepository = $readerRepository;
        $this->leaseRepository = $leaseRepository;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route(path="/books", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $filters = $request->query->all();

        return $this->json(
            $this->bookRepository->findAvailable($filters ?: [])
        );
    }

    /**
     * @Route(path="/books", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $content = $this->getContent($request);

        $authors = $this->authorRepository->findBy(['id' => $content['author_ids']]);

        $this->entityManager->persist(BookFactory::build($authors, $content));
        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    /**
     * @Route(path="/books/{id}", methods={"PUT"})
     */
    public function update(int $id, Request $request): Response
    {
        $content = $this->getContent($request);
        $authors = $this->authorRepository->findBy(['id' => $content['author_ids']]);

        $book = $this->bookRepository->find($id);
        BookFactory::addAuthors($book, $authors);
        BookFactory::fillWhitelistedValues($book, $content);

        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    /**
     * @Route(path="/books/{id}", methods={"DELETE"})
     */
    public function delete(int $id, Request $request): Response
    {
        $this->entityManager->remove($this->bookRepository->find($id));
        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    /**
     * @Route(path="/books/{bookId}/lease", methods={"POST"})
     * @throws \Exception
     */
    public function lease(int $bookId, Request $request): Response
    {
        $content = $this->getContent($request);
        $book = $this->bookRepository->find($bookId);
        $reader = $this->readerRepository->find($content['reader_id']);

        $readerLeases = $this->leaseRepository->count(['reader' => $reader]);

        if ($readerLeases == self::MAX_ALLOWED_READER_LEASES) {
            throw new \Exception('readers can not lease more than three books at the same time');
        }

        $lease = LeaseFactory::build($book, $reader, $content['return_at']);

        $this->entityManager->persist($lease);
        $this->entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }

    private function getContent(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }
}
