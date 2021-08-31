<?php
namespace App\Tests\Controller;

use App\Controller\Api\BookController;
use App\Entity\Book;
use App\Service\BookManager;
use DateTime;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookControllerTest extends TestCase
{
    /** @var BookController|MockObject */
    private $bookController;

    /** @var BookManager|MockObject */
    private $bookManagerMock;

    /** @var ParamFetcherInterface|MockObject */
    private $paramFetcherMock;

    /** @var Request|MockObject */
    private $requestMock;

    /** @var Form|MockObject */
    private $formMock;

    protected function setUp(): void
    {
        $this->bookManagerMock = $this->getMockBuilder(BookManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->paramFetcherMock = $this->getMockBuilder(ParamFetcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->bookController = new BookController($this->bookManagerMock);

        $this->requestMock = $this->getMockBuilder(Request::class)->getMock();

        $this->formMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @throws ReflectionException
     */
    public function testGetBookAction()
    {
        $book = $this->getItemData();

        $result = $this->bookController->getBook($book);

        $response = View::create($book);

        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetBooksAction()
    {
        $items = $this->getItemsData();

        $pagination = new SlidingPagination([]);
        $pagination->setCurrentPageNumber(1);
        $pagination->setItems(array_slice($items, 0, 10));
        $pagination->setTotalItemCount(count($items));
        $pagination->setItemNumberPerPage(10);

        $this->bookManagerMock->method('getBooks')->willReturn($pagination);
        $result = $this->bookController->getBooks($this->paramFetcherMock);
        $response = View::create($pagination, Response::HTTP_OK);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testPostBook()
    {
        $book = $this->getItemData();
        $this->updateFormMock($book);
        $this->bookManagerMock->method('postBook')->willReturn($this->formMock);
        $result = $this->bookController->postBook($this->requestMock);
        $response = View::createRouteRedirect('app_api_book_getbook', ['id' => $book->getId()], Response::HTTP_CREATED);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testPutBook()
    {
        $book = $this->getItemData();
        $this->updateFormMock($book);

        $this->bookManagerMock->method('putBook')->willReturn($this->formMock);
        $result = $this->bookController->putBook($this->requestMock, $book);
        $response = View::createRouteRedirect('app_api_book_getbook', ['id' => $book->getId()], Response::HTTP_NO_CONTENT);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testLeaseBook()
    {
        $book = $this->getItemData();
        $this->updateFormMock($book);

        $this->bookManagerMock->method('leaseBook')->willReturn($this->formMock);
        $result = $this->bookController->leaseBook($this->requestMock, $book);
        $response = View::createRouteRedirect('app_api_book_getbook', ['id' => $book->getId()], Response::HTTP_NO_CONTENT);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testDeleteBook()
    {
        $book = $this->getItemData();
        $result = $this->bookController->deleteBook($book);
        $response = View::create(null, Response::HTTP_NO_CONTENT);
        $this->assertEquals($result, $response);
    }

    /**
     * @return Book
     * @throws ReflectionException
     */
    public function getItemData(): Book
    {
        $object = new Book();
        $this->setIdToEntity($object, 1);
        $object->setAuthor('TestAuthor');
        $object->setReturnDate(new DateTime());

        return $object;
    }

    /**
     * @return Book|array
     * @throws ReflectionException
     */
    private function getItemsData(): array
    {
        $items = [];
        for($i = 1; $i <= 25; $i++) {
            $object = new Book();
            $this->setIdToEntity($object, 1);
            $object->setAuthor(sprintf('TestAuthor_%d', $i));
            $object->setReturnDate(new DateTime());
            array_push($items, $object);
        }

        return $items;
    }

    /**
     * @param object $entity
     * @param int $value
     * @throws ReflectionException
     */
    private function setIdToEntity(object $entity, int $value): void
    {
        $reflectionClass = new ReflectionClass(get_class($entity));
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($entity, $value);
    }

    /**
     * @param Book $object
     * @return void
     */
    private function updateFormMock(Book $object): void
    {
        $this->formMock->method('isValid')->willReturn(true);
        $this->formMock->method('getData')->willReturn($object);
    }
}