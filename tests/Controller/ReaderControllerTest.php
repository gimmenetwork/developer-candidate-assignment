<?php
namespace App\Tests\Controller;

use App\Controller\Api\ReaderController;
use App\Entity\Book;
use App\Entity\Reader;
use App\Service\ReaderManager;
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

class ReaderControllerTest extends TestCase
{
    /** @var ReaderController|MockObject */
    private $readerController;

    /** @var ReaderManager|MockObject */
    private $readerManagerMock;

    /** @var ParamFetcherInterface|MockObject */
    private $paramFetcherMock;

    /** @var Request|MockObject */
    private $requestMock;

    /** @var Form|MockObject */
    private $formMock;

    protected function setUp(): void
    {
        $this->readerManagerMock = $this->getMockBuilder(ReaderManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->paramFetcherMock = $this->getMockBuilder(ParamFetcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->readerController = new ReaderController($this->readerManagerMock);

        $this->requestMock = $this->getMockBuilder(Request::class)->getMock();

        $this->formMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @throws ReflectionException
     */
    public function testGetReaderAction(): void
    {
        $reader = $this->getItemData();

        $result = $this->readerController->getReader($reader);

        $response = View::create($reader);

        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetReadersAction(): void
    {
        $items = $this->getItemsData();

        $pagination = new SlidingPagination([]);
        $pagination->setCurrentPageNumber(1);
        $pagination->setItems(array_slice($items, 0, 10));
        $pagination->setTotalItemCount(count($items));
        $pagination->setItemNumberPerPage(10);

        $this->readerManagerMock->method('getReaders')->willReturn($pagination);
        $result = $this->readerController->getReaders($this->paramFetcherMock);
        $response = View::create($pagination, Response::HTTP_OK);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testPostReader(): void
    {
        $reader = $this->getItemData();
        $this->updateFormMock($reader);
        $this->readerManagerMock->method('postReader')->willReturn($this->formMock);
        $result = $this->readerController->postReader($this->requestMock);
        $response = View::createRouteRedirect('app_api_reader_getreader', ['id' => $reader->getId()], Response::HTTP_CREATED);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testPutReader(): void
    {
        $reader = $this->getItemData();
        $this->updateFormMock($reader);

        $this->readerManagerMock->method('putReader')->willReturn($this->formMock);
        $result = $this->readerController->putReader($this->requestMock, $reader);
        $response = View::createRouteRedirect('app_api_reader_getreader', ['id' => $reader->getId()], Response::HTTP_NO_CONTENT);
        $this->assertEquals($result, $response);
    }

    /**
     * @throws ReflectionException
     */
    public function testDeleteReader(): void
    {
        $reader = $this->getItemData();
        $result = $this->readerController->deleteReader($reader);
        $response = View::create(null, Response::HTTP_NO_CONTENT);
        $this->assertEquals($result, $response);
    }

    /**
     * @return Reader
     * @throws ReflectionException
     */
    public function getItemData(): Reader
    {
        $object = new Reader();
        $this->setIdToEntity($object, 1);
        $object->setName('TestReader');

        return $object;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function getItemsData(): array
    {
        $items = [];
        for($i = 1; $i <= 25; $i++) {
            $object = new Reader();
            $this->setIdToEntity($object, 1);
            $object->setName(sprintf('TestReader_%d', $i));
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
    private function updateFormMock(Reader $object): void
    {
        $this->formMock->method('isValid')->willReturn(true);
        $this->formMock->method('getData')->willReturn($object);
    }
}