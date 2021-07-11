<?php

namespace App\Tests;

use App\Service\ReaderService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

class ApiTest extends WebTestCase
{
    private AbstractBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testUnauthenticatedRequestReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/api/get-books',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{}'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);
    }

    public function testBookListSuccessResponse(): void
    {
        $this->successAuth();

        $this->client->request(
            'POST',
            '/api/get-books',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{}'
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData[0]);
        $this->assertArrayHasKey('name', $responseData[0]);
        $this->assertArrayHasKey('is_available', $responseData[0]);
        $this->assertIsBool($responseData[0]['is_available']);
    }

    public function testAddBookInvalidParametersReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/api/add-book',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"name":"test","author":"test"}'
        );

        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $responseData);
    }

    public function testAboveLeaseLimitReturnsError(): void
    {
        $readerName = 'testReaderLeaseLimit_' . rand(0, 1000);
        $bookName = 'testReaderLeaseLimit_Book_' . rand(0, 1000);
        $authorName = 'testReaderLeaseLimit_Author_' . rand(0, 1000);

        $this->successAuth();

        //Add new reader
        $this->client->request(
            'POST',
            '/api/add-reader',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"name":"' . $readerName . '"}'
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        //Get added reader id
        $this->client->request(
            'POST',
            '/api/get-readers',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"name":"' . $readerName . '"}'
        );
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $readerId = $responseData[0]['id'];


        //Add new book
        $this->client->request(
            'POST',
            '/api/add-book',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"name":"' . $bookName . '","author":"' . $authorName . '","genre":"test"}'
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        //Get added book id
        $this->client->request(
            'POST',
            '/api/get-books',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"author":"' . $authorName . '"}'
        );
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $bookId = $responseData[0]['id'];


        //Lease book until error
        for ($i = 0; $i <= ReaderService::LEASE_LIMIT; $i++) {
            $this->client->request(
                'GET',
                '/api/lease-book/' . $bookId . '/' . $readerId
            );
            $response = $this->client->getResponse();
            if ($i < ReaderService::LEASE_LIMIT) {
                $this->assertEquals(200, $response->getStatusCode());
            }else{
                $this->assertEquals(400, $response->getStatusCode());
                $responseData = json_decode($response->getContent(), true);
                $this->assertArrayHasKey('error', $responseData);
            }
        }
    }

    private function successAuth()
    {
        $this->client->request(
            'POST',
            '/api/login',
            [], [], ['CONTENT_TYPE' => 'application/json'],
            '{"username":"admin","password":"password"}'
        );

        $this->client->getResponse();
    }


}
