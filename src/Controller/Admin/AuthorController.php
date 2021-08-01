<?php

namespace App\Controller\Admin;

use App\Form\AuthorFormType;
use App\Manager\AuthorManager;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/authors', name: 'authors_')]
class AuthorController extends BaseController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'manager' => AuthorManager::class,
        ]);
    }

    protected function getFormType(): string
    {
        return AuthorFormType::class;
    }
}
