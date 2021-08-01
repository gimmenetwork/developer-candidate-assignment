<?php declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use JetBrains\PhpStorm\ArrayShape;

final class PaginatorService
{
    /**
     * @var int
     */
    public const LIMIT = 10;

    /**
     * @var int
     */
    private int $currentPage = 1;

    /**
     * @var int
     */
    private int $pageSize = self::LIMIT;

    /**
     * @var Query|QueryBuilder|null
     */
    private Query|QueryBuilder|null $queryOrBuilder;

    /**
     * @param $queryOrBuilder
     * @param int $currentPage
     * @param int $pageSize
     *
     * @return PaginatorService
     */
    public static function create($queryOrBuilder, int $currentPage, int $pageSize): self
    {
        return (new self())
            ->setCurrentPage($currentPage)
            ->setPageSize($pageSize)
            ->setQueryOrBuilder($queryOrBuilder);
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        if ($this->currentPage < 1) {
            return 1;
        }

        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     *
     * @return $this;
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @return Query|QueryBuilder|null
     */
    public function getQueryOrBuilder(): Query|QueryBuilder|null
    {
        return $this->queryOrBuilder;
    }

    /**
     * @param $queryOrBuilder
     *
     * @return $this
     */
    public function setQueryOrBuilder($queryOrBuilder): self
    {
        $this->queryOrBuilder = $queryOrBuilder;

        return $this;
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    #[ArrayShape([
        'results'         => '\ArrayIterator|\Traversable',
        'currentPage'     => 'int',
        'hasPreviousPage' => 'bool',
        'hasNextPage'     => 'bool',
        'previousPage'    => 'int|null',
        'nextPage'        => 'int|null',
        'numPages'        => 'int',
        'haveToPaginate'  => 'bool',
        'total'           => 'int',
        'pageSize'        => 'int',
    ])]
    public function createPaginator(): array
    {
        $currentPage = $this->getCurrentPage();
        $pageSize = $this->getPageSize();
        $firstResult = ($currentPage - 1) * $pageSize;

        $queryOrBuilder = $this->getQueryOrBuilder();

        if ($queryOrBuilder === null) {
            throw new \InvalidArgumentException('Paginator must have query or queryBuilder.');
        }

        if (!$queryOrBuilder instanceof AbstractQuery) {
            $query = $queryOrBuilder
                ->setFirstResult($firstResult)
                ->setMaxResults($pageSize)
                ->getQuery();
        } else {
            $query = $queryOrBuilder;
        }

        if ($query->getFirstResult() === null) {
            $query->setFirstResult($firstResult);
        }

        if ($query->getMaxResults() === null) {
            $query->setMaxResults($pageSize);
        }

        $paginator = new DoctrinePaginator($query);
        $numResults = $paginator->count();

        $hasPreviousPage = $currentPage > 1;
        $hasNextPage = ($currentPage * $pageSize) < $numResults;

        return [
            'results' => $paginator->getIterator(),
            'currentPage' => $currentPage,
            'hasPreviousPage' => $hasPreviousPage,
            'hasNextPage' => $hasNextPage,
            'previousPage' => $hasPreviousPage ? $currentPage - 1 : null,
            'nextPage' => $hasNextPage ? $currentPage + 1 : null,
            'numPages' => (int) ceil($numResults / $pageSize),
            'haveToPaginate' => $numResults > $pageSize,
            'total' => $numResults,
            'pageSize' => $pageSize,
        ];
    }
}
