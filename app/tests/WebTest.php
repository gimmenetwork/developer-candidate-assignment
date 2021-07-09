<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

class WebTest extends WebTestCase
{
    private AbstractBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testHomePage(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Books');
        $this->assertGreaterThanOrEqual(50, count($crawler->filter('.book-row')));
    }

    public function testAddBookWithoutLoginRedirectsHomepage(): void
    {
        $this->client->request('GET', '/new-book');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Books');
    }

    public function testAddReaderWithoutLoginRedirectsHomepage(): void
    {
        $this->client->request('GET', '/new-reader');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Books');
    }

    public function testFailedLogin(): void
    {
        $this->client->request('GET', '/');

        $this->client->clickLink('Login');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Login', [
            'form[username]' => 'invalid',
            'form[password]' => 'data',
        ]);

        $this->assertSelectorTextContains('span', 'Wrong credentials');
    }

    public function testSuccessLogin(): void
    {
        $this->client->request('GET', '/');

        $this->client->clickLink('Login');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Login', [
            'form[username]' => 'admin',
            'form[password]' => 'password',
        ]);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h3', 'Books');

        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(1, $session->get('isLogin'));
    }

    public function testAddBook(): void
    {
        $bookName = 'testBook'.rand(0,1000);
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Login', [
            'form[username]' => 'admin',
            'form[password]' => 'password',
        ]);
        $this->client->followRedirect();

        $this->client->clickLink('Add Book');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Add', [
            'form[name]' => $bookName,
            'form[author]' => 'testAuthor',
            'form[genre]' => 'horror',
        ]);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('span', $bookName);
    }

    public function testDuplicateBookName(): void
    {
        $bookName = 'testBook'.rand(0,1000);

        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Login', [
            'form[username]' => 'admin',
            'form[password]' => 'password',
        ]);
        $this->client->followRedirect();

        $this->client->clickLink('Add Book');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Add', [
            'form[name]' => $bookName,
            'form[author]' => 'testAuthor',
            'form[genre]' => 'horror',
        ]);
        $this->client->followRedirect();

        $this->client->clickLink('Add Book');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Add', [
            'form[name]' => $bookName,
            'form[author]' => 'testAuthor',
            'form[genre]' => 'horror',
        ]);

        $this->assertSelectorTextContains('span', 'Duplicate Book name');
    }

    public function testDuplicateReaderName(): void
    {
        $readerName = 'testReader'.rand(0,1000);

        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Login', [
            'form[username]' => 'admin',
            'form[password]' => 'password',
        ]);
        $this->client->followRedirect();

        $this->client->clickLink('Add Reader');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Add', [
            'form[name]' => $readerName
        ]);
        $this->client->followRedirect();

        $this->client->clickLink('Add Reader');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Add', [
            'form[name]' => $readerName
        ]);

        $this->assertSelectorTextContains('span', 'Duplicate reader name');
    }

}
