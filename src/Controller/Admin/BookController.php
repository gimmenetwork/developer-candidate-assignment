<?php

namespace App\Controller\Admin;

use App\Entity\ReaderBook;
use App\Form\BookFormType;
use App\Form\LeaseFormType;
use App\Manager\BookManager;
use App\Service\FormErrorSerializerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/books', name: 'books_')]
class BookController extends BaseController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'manager' => '?'.BookManager::class,
        ]);
    }

    protected function getFormType(): string
    {
        return BookFormType::class;
    }

    #[Route('/lease/{id}', name: 'lease')]
    public function lease(Request $request, TranslatorInterface $translator, FormErrorSerializerService $formErrorSerializer, int $id)
    {
        $manager = $this->get('manager');
        $entity = $manager->findOne($id);

        if (!$entity) {
            return $this->json(['message' => $translator->trans('not.found')], Response::HTTP_NOT_FOUND);
        }

        if (!$entity->getIsAvailable()) {
            return $this->json(['message' => $translator->trans('book.not.available')], Response::HTTP_BAD_REQUEST);
        }

        $readerBook = new ReaderBook();
        $readerBook->setBook($entity);

        $form = $this->createForm(LeaseFormType::class, $readerBook);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                return $this->json($formErrorSerializer->convertFormToArray($form), Response::HTTP_BAD_REQUEST);
            }

            $manager->lease($readerBook);

            return new JsonResponse();
        }

        $view  = $this->renderView('pages/books/lease_form.html.twig', ['entity' => $entity, 'form' => $form->createView()]);

        return $this->json(['data' => $view]);
    }
}
