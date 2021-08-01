<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class SecurityControllerTest extends BaseWebTestCase
{
    public function testHomepageWithoutLoginRedirectToLogin(): void
    {
        $this->client->request('GET', '/');
        $this->client->followRedirects();

        $this->assertResponseRedirects('/login');
    }

    public function testLoginPage(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Sign in to your account');
    }

    public function testLoginSuccessfully(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Sign in', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/books');
    }

    public function testLoginFailed(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Sign in', [
            'email' => 'admin@example.com',
            'password' => 'wrong password',
        ]);
        $this->client->followRedirects();

        $this->assertResponseRedirects('/login');
    }
}
