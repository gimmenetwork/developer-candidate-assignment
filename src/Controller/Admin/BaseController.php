<?php

namespace App\Controller\Admin;

use App\Entity\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    abstract protected function getFormType(): string;

    #[Route(name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        [$prefix] = explode('_', $request->attributes->get('_route'));

        $paginator = $this->get('manager')->getAll(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('pages/'.$prefix.'/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm($this->getFormType());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                dd(1);
            }

            $this->get('manager')->save($form->getData());

            $this->addFlash('success', 'The book created successfully.');

            return $this->redirectToList($request);
        }

        return $this->render('default/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, int $id): Response
    {
        $entity = $this->get('manager')->findOne($id);

        if (!$entity) {
            $this->addFlash('error', 'not.found');

            return $this->redirectToList($request);
        }

        $form = $this->createForm($this->getFormType(), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                dd(1);
            }

            $this->get('manager')->save($form->getData());

            $this->addFlash('success', 'The book updated successfully.');

            return $this->redirectToList($request);
        }

        return $this->render('default/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, TranslatorInterface $translator, int $id): Response
    {
        $manager = $this->get('manager');

        $entity = $manager->findOne($id);

        if (!$entity) {
            $this->addFlash('error', $translator->trans('not.found'));

            return $this->redirectToList($request);
        }

        if ($entity instanceof Reader) {
            $this->addFlash('error', $translator->trans('unauthorized.action'));

            return $this->redirectToList($request);
        }

        $this->addFlash('success', $translator->trans('delete.success'));

        $manager->delete($entity);

        return $this->redirectToList($request);
    }

    private function redirectToList(Request $request): RedirectResponse
    {
        [$firstPart]  = explode('_', $request->get('_route'));

        return $this->redirectToRoute($firstPart . '_index');
    }
}
