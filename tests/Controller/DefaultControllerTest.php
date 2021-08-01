<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class DefaultControllerTest extends BaseWebTestCase
{
    public function testHomepageRedirectToBooks(): void
    {
        $this->login();

        $this->client->request('GET', '/');
        $this->client->followRedirects();

        $this->assertResponseRedirects('/books');
    }
}
