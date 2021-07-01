<?php


namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class IndexController
{

    /**
     * @Route("/")
     **/
    public function index(Environment $twig): Response
    {
        return new Response(
            $twig->render(
                'base.html.twig', [
                ]
            )
        );
    }
}