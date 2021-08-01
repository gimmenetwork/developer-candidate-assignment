<?php

namespace App\Controller\Admin;

use App\Form\GenreFormType;
use App\Manager\GenreManager;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/genres', name: 'genres_')]
class GenreController extends BaseController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'manager' => GenreManager::class,
        ]);
    }

    protected function getFormType(): string
    {
        return GenreFormType::class;
    }
}
