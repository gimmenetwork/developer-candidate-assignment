<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Readers;

class ReaderControllerTest extends WebTestCase
{
    private $rootUrl = "/readers/";

    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->rootUrl);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'no records found');
    }


    /**
     * @depends testIndex
     * @dataProvider additionProvider
     */
    public function testNew(string $name)
    {
        $client = static::createClient();
        $client->request('GET', $this->rootUrl.'new');

        $crawler = $client->submitForm('Save', [
            'readers[name]' => $name,
        ]);

        $this->assertResponseRedirects($this->rootUrl, 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(2)', $name);
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
        $crawler = $client->request('GET', $this->rootUrl.'3/edit');

        $this->assertResponseIsSuccessful();

        $name = "Test Girl";

        $crawler = $client->submitForm('Update', [
            'readers[name]' => $name,
        ]);

        $this->assertResponseRedirects($this->rootUrl, 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(2)', $name);

    }

    /**
     * @depends testEdit
     */
    public function testDelete() 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->rootUrl.'3/edit');

        $crawler = $client->submitForm('Delete');

        $this->assertResponseRedirects($this->rootUrl, 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:first-child', '2');
    }

    public function additionProvider(): array
    {
        return [
            ['John Doe'],
            ['Jane Doe'],
            ['another one'],
        ];
    }
}
