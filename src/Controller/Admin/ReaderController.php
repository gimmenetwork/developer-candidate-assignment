<?php

namespace App\Controller\Admin;

use App\Form\ReaderFormType;
use App\Manager\ReaderManager;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/readers', name: 'readers_')]
class ReaderController extends BaseController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'manager' => ReaderManager::class,
        ]);
    }

    protected function getFormType(): string
    {
        return ReaderFormType::class;
    }
}
