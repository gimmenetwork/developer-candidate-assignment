<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Readers;

class ReaderControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/readers/');

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
        $client->request('GET', '/readers/new');

        $crawler = $client->submitForm('Save', [
            'readers[name]' => $name,
        ]);

        $this->assertResponseRedirects('/readers/', 303);

        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('table tbody tr:last-child td:nth-child(2)', $name);
    }

    public function additionProvider(): array
    {
        return [
            ['John Doe'],
            ['Jane Doe'],
        ];
    }
}
