<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/{reactRoute}", defaults={"reactRoute": null})
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}