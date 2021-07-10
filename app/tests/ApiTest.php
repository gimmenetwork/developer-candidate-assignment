<?php

namespace App\Tests;

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

    public function testBookListSuccessResponse(): void
    {
        $this->client->request(
            'POST',
            '/api/get-books',
            [],[],['CONTENT_TYPE' => 'application/json'],
            '{}'
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id',$responseData[0]);
        $this->assertArrayHasKey('name',$responseData[0]);
        $this->assertArrayHasKey('is_available',$responseData[0]);
        $this->assertIsBool($responseData[0]['is_available']);
    }


}
