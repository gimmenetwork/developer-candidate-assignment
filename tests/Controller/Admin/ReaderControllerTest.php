<?php

namespace App\Tests\Controller\Admin;

use App\Tests\BaseWebTestCase;

class ReaderControllerTest extends BaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testReaderListPage(): void
    {
        $this->client->request('GET', '/readers');

        $this->assertPageTitleSame('Readers');
    }

    public function testReaderListPageAddButtonRedirectToForm(): void
    {
        $this->client->request('GET', '/readers');

        $this->client->clickLink('Add');

        $this->assertPageTitleSame('Create a reader');
    }

    public function testReaderCreateSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/readers/create');

        $form = $crawler->selectButton('Save')->form();

        $form['reader_form[name]'] = 'Test reader';

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/readers');
    }

    public function testReaderEditSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/readers');

        $this->client->click($crawler->selectLink('Edit')->first()->link());

        $this->client->submitForm('Save', [
            'reader_form[name]' =>  'Edited reader name'
        ]);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/readers');
    }
}
