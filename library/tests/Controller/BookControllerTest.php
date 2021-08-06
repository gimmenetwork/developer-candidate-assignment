<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class BookControllerTest extends WebTestCase
{
    private static $client;
    private $rootUrl = "/book/";

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
        $crawler = $client->request('GET', $this->rootUrl);

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
        $client->request('GET', $this->rootUrl.'new');

        $crawler = $client->submitForm('Save', [
            'book[name]' => $name,
            'book[author]' => $author,
            'book[genre]' => $genre,
        ]);

        $this->assertResponseRedirects($this->rootUrl, 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(2)', $name);
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(3)', $author);
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(4)', $genre);
    }

    /**
     * @depends testNew
     * @dataProvider additionProvider
     */
    public function testShow()
    {
        static $id = 1;
        $client = static::createClient();
        $crawler = $client->request('GET', $this->rootUrl.$id);

        $this->assertResponseIsSuccessful();

        $data = $crawler->filter('table tbody tr td:nth-child(2)')
        ->each(function ($node, $i) {
            return $node->text();
        });
        
        $args = func_get_args();
        array_unshift($args, $id);
        array_pop($args);

        $this->assertEquals($args, array_slice($data, 0, 4));

        $id++;
    }

    /**
     * @depends testShow
     */
    public function testEdit() 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->rootUrl.'7/edit');

        $this->assertResponseIsSuccessful();

        $name = "The Diary Of A Young Girl";
        $author = "Anne Frank";
        $genre = "Romance";

        $crawler = $client->submitForm('Update', [
            'book[name]' => $name.' edit',
            'book[author]' => $author.' edit',
            'book[genre]' => $genre.' edit',
        ]);

        $this->assertResponseRedirects($this->rootUrl, 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(2)', $name.' edit');
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(3)', $author.' edit');
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(4)', $genre.' edit');

    }

    /**
     * @depends testEdit
     */
    public function testDelete() 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->rootUrl.'7/edit');

        $crawler = $client->submitForm('Delete');

        $this->assertResponseRedirects($this->rootUrl, 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:first-child', '6');
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