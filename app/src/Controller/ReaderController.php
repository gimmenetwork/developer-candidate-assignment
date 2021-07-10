<?php

namespace App\Controller;

use App\Contracts\AuthenticationInterface;
use App\Entity\Reader;
use App\Form\Type\ReaderType;
use App\Service\ReaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReaderController extends AbstractController implements AuthenticationInterface
{
    public function __construct(
        private ReaderService $readerService
    )
    {
    }

    #[Route('/new-reader', name: 'newReader')]
    public function addReader(Request $request): Response
    {
        $form = $this->createForm(ReaderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->readerService->saveReader($form->getData());

                $this->addFlash('success', $form->getData()['name'] . ' is saved');
                return $this->redirectToRoute('homepage');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        $readers = $this->readerService->getAllReaders();

        return $this->render('newreader.html.twig', array(
            'form' => $form->createView(),
            'readers' => $readers
        ));
    }

    #[Route('/edit-reader/{reader}', name: 'editReader')]
    public function editReader(Reader $reader, Request $request): Response
    {
        $form = $this->createForm(ReaderType::class, $reader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->readerService->editReader($form->getData());

                $this->addFlash('success', $form->getData()->getName() . ' is edited');
                return $this->redirectToRoute('newReader');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('editreader.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
