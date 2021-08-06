<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class BookControllerTest extends WebTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        static::$client = static::createClient();

        $entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //In case leftover entries exist
        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and recreate tables for all entities
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testIndex()
    {
        $client = static::$client;
        $crawler = $client->request('GET', '/book/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'no records found');
    }


    /**
     * @depends testIndex
     * @dataProvider additionProvider
     */
    public function testNew(string $name, string $author, ?string $genre)
    {
        $client = static::createClient();
        $client->request('GET', '/book/new');

        $crawler = $client->submitForm('Save', [
            'book[name]' => $name,
            'book[author]' => $author,
            'book[genre]' => $genre,
        ]);

        $this->assertResponseRedirects('/book/', 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(2)', $name);
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(3)', $author);
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(4)', $genre);
    }

    public function additionProvider(): array
    {
        return [
            ["To Kill a Mockingbird", "Harper Lee", "Fantasy"],
            ["1984", "George Orwell", "Documentary"],
            ["Harry Potter and the Philosopher's Stone", "J.K. Rowling", "Fantasy"],
            ["The Lord of the Rings", "J.R.R. Tolkien", "Fantasy"],
            ["The Great Gatsby", "F", "Romance"],
            ["Pride and Prejudice", "Jane Austen", "Romance"],
            ["The Diary Of A Young Girl", "Anne Frank", "Romance"],
        ];
    }
}