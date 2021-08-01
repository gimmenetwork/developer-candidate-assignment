<?php

namespace App\Tests\Controller\Admin;

use App\Tests\BaseWebTestCase;

class GenreControllerTest extends BaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testGenreListPage(): void
    {
        $this->client->request('GET', '/genres');

        $this->assertPageTitleSame('Genres');
    }

    public function testGenreListPageAddButtonRedirectToForm(): void
    {
        $this->client->request('GET', '/genres');

        $this->client->clickLink('Add');

        $this->assertPageTitleSame('Create a genre');
    }

    public function testGenreCreateSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/genres/create');

        $form = $crawler->selectButton('Save')->form();

        $form['genre_form[name]'] = 'Test genre';

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/genres');
    }

    public function testGenreEditSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/genres');

        $this->client->click($crawler->selectLink('Edit')->first()->link());

        $this->client->submitForm('Save', [
            'genre_form[name]' =>  'Edited genre name'
        ]);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/genres');
    }
}
