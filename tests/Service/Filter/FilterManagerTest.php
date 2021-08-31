<?php
namespace App\Tests\Service\Filter;

use App\Entity\Book;
use App\Service\Filter\BookFilterHandler;
use App\Service\Filter\FilterManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcherInterface;
use PHPUnit\Framework\TestCase;

class FilterManagerTest extends TestCase
{
    public function testApplyFiltersForBookQueryBuilder(): void
    {
        $paramFetcherMock = $this->getMockBuilder(ParamFetcherInterface::class)->getMock();
        $paramFetcherMock->method('all')->willReturn(
            [
                'name' => 'testName',
                'genre' => 'testGenre',
            ]
        );

        $entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $entityManagerMock->method('getExpressionBuilder')->willReturn(new Expr());
        $queryBuilder = new QueryBuilder($entityManagerMock);

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->join('b.genre', 'genre')
        ;

        $filterManager = new FilterManager(
            [new BookFilterHandler()]
        );

        $filterManager->applyFilters($queryBuilder, $paramFetcherMock);

        /** @var Expr\Andx  $whereParts */
        $whereParts = $queryBuilder->getDQLPart('where');
        $this->assertInstanceOf(Expr\Andx::class, $whereParts);
        $this->assertCount(2, $whereParts->getParts());
    }
}