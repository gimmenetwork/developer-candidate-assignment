<?php

namespace App\Tests\Controller\Admin;

use App\Tests\BaseWebTestCase;

class BookControllerTest extends BaseWebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testBookListPage(): void
    {
        $this->client->request('GET', '/books');

        $this->assertPageTitleSame('Books');
    }

    public function testBookListPageAddButtonRedirectToForm(): void
    {
        $this->client->request('GET', '/books');

        $this->client->clickLink('Add');

        $this->assertPageTitleSame('Create a book');
    }

    public function testBookCreateSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/books/create');

        $form = $crawler->selectButton('Save')->form();

        $form['book_form[name]'] = 'Test book';
        $this->setOption($form, 'book_form[author]');
        $this->setOption($form, 'book_form[genre]');
        $form['book_form[isbn]'] = '123';
        $form['book_form[summary]'] = 'summary';

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/books');
    }

    public function testBookEditSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/books');

        $this->client->click($crawler->selectLink('Edit')->first()->link());

        $this->client->submitForm('Save', [
            'book_form[name]' =>  'Edited book name'
        ]);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/books');
    }

    public function testLeaseSuccessfully(): void
    {

    }

    private function setOption(&$form, string $key, bool $isEmpty = false): void
    {
        $options = array_filter($form[$key]->availableOptionValues(), static function ($item) use ($isEmpty) {
            return is_numeric($item);
        });

        if ($isEmpty)  {
            $form[$key]->disableValidation()->select('invalid option');
        } else {
            $form[$key]->select(current($options));
        }
    }
}
