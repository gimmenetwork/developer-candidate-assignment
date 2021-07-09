<?php

namespace App\Controller;

use App\Contracts\AuthenticationInterface;
use App\Service\ReaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReaderController extends AbstractController implements AuthenticationInterface
{
    public function __construct(
        private ReaderService $readerService
    ){

    }

    #[Route('/new-reader', name: 'newReader')]
    public function addBook(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('add', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->readerService->saveReader($form->getData());

            $this->addFlash('success',$form->getData()['name'].' is saved');
            return $this->redirectToRoute('homepage');
        }

        return $this->render ( 'newreader.html.twig', array (
            'form' => $form->createView()
        ));
    }
}
