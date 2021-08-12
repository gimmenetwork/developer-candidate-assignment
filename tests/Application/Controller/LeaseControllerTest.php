<?php

declare(strict_types=1);

namespace Application\Controller;

use GimmeBook\Application\Controller\LeaseController;
use GimmeBook\Application\Service\RedirectToIndexResponseProducer;
use GimmeBook\Infrastructure\Entity\Book\Book;
use GimmeBook\Infrastructure\Repository\Book\BookRepositoryInterface;
use GimmeBook\Infrastructure\Repository\LeaseRepositoryInterface;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \GimmeBook\Application\Controller\LeaseController
 * @codeCoverageIgnore
 */
class LeaseControllerTest extends MockeryTestCase
{
    private LeaseController $leaseController;
    private LeaseRepositoryInterface|MockInterface $leaseRepository;
    private BookRepositoryInterface|MockInterface $bookRepository;

    protected function setUp(): void
    {
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        $urlGenerator->expects('generate')->andReturn('url');

        $this->leaseRepository = Mockery::mock(LeaseRepositoryInterface::class);
        $this->bookRepository = Mockery::mock(BookRepositoryInterface::class);
        $readerRepository = Mockery::mock(ReaderRepositoryInterface::class);

        $this->leaseController = new LeaseController(
            new RedirectToIndexResponseProducer($urlGenerator),
            $this->leaseRepository,
            $this->bookRepository,
            $readerRepository,
        );
    }

    private function assertError(Request $request, string $message, string $failMessage): void
    {
        $cookies = $this->leaseController->leaseBook($request, 1)
            ->headers
            ->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'error') {
                self::assertEquals($message, $cookie->getValue());
                return;
            }
        }
        self::fail($failMessage);
    }

    public function testForbiddenIfManyBooksNotReturned(): void
    {
        $request = new Request(attributes: ['readerId' => 1]);
        // 3 books
        $this->leaseRepository->expects('getBySpecification')->andReturn(range(1, 3));

        $this->assertError(
            $request,
            'You need to return some book to lease this one',
            'No error about books limit',
        );
    }

    public function testBookOutOfStock(): void
    {
        $request = new Request(attributes: ['readerId' => 1]);
        $this->leaseRepository->expects('getBySpecification')->andReturn([]);
        $book = Mockery::mock(Book::class);
        $book->expects('getCountInStock')->andReturn(0);
        $this->bookRepository->expects('getOneBySpecification')->andReturn($book);

        $this->assertError(
            $request,
            'This book cannot be leased, try later please',
            'No error about books out of stock',
        );
    }
}
