<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        parent::setUp();
    }

    public function login(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneByEmail('admin@example.com');

        $this->client->loginUser($user);
    }
}
