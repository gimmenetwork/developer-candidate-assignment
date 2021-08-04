<?php

namespace App\Controller;

use App\Entity\Readers;
use App\Form\ReadersType;
use App\Repository\ReadersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/readers")
 */
class ReadersController extends AbstractController
{
    /**
     * @Route("/", name="readers_index", methods={"GET"})
     */
    public function index(ReadersRepository $readersRepository): Response
    {
        return $this->render('readers/index.html.twig', [
            'readers' => $readersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="readers_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reader = new Readers();
        $form = $this->createForm(ReadersType::class, $reader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reader);
            $entityManager->flush();

            return $this->redirectToRoute('readers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('readers/new.html.twig', [
            'reader' => $reader,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="readers_show", methods={"GET"})
     */
    public function show(Readers $reader): Response
    {
        return $this->render('readers/show.html.twig', [
            'reader' => $reader,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="readers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Readers $reader): Response
    {
        $form = $this->createForm(ReadersType::class, $reader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('readers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('readers/edit.html.twig', [
            'reader' => $reader,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="readers_delete", methods={"POST"})
     */
    public function delete(Request $request, Readers $reader): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reader->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reader);
            $entityManager->flush();
        }

        return $this->redirectToRoute('readers_index', [], Response::HTTP_SEE_OTHER);
    }
}
