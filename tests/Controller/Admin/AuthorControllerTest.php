<?php

namespace App\Tests\Controller\Admin;

use App\Tests\BaseWebTestCase;

class AuthorControllerTest extends BaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testAuthorListPage(): void
    {
        $this->client->request('GET', '/authors');

        $this->assertPageTitleSame('Authors');
    }

    public function testAuthorListPageAddButtonRedirectToForm(): void
    {
        $this->client->request('GET', '/authors');

        $this->client->clickLink('Add');

        $this->assertPageTitleSame('Create a author');
    }

    public function testAuthorCreateSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/authors/create');

        $form = $crawler->selectButton('Save')->form();

        $form['author_form[name]'] = 'Test author';

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/authors');
    }

    public function testAuthorEditSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/authors');

        $this->client->click($crawler->selectLink('Edit')->first()->link());

        $this->client->submitForm('Save', [
            'author_form[name]' =>  'Edited author name'
        ]);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/authors');
    }
}
