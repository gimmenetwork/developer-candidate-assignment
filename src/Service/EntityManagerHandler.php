<?php
namespace App\Service;

use App\Service\Filter\FilterManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class handles the specific actions [create, edit and delete]
 * Class EntityManager
 *
 * @package App\Service
 */
class EntityManagerHandler
{
    /** @var EntityManagerInterface */
    protected EntityManagerInterface $entityManager;

    /** @var FormFactoryInterface */
    protected FormFactoryInterface $formFactory;

    /** @var FilterManager  */
    protected FilterManager $filterManager;

    /** @var PaginatorInterface */
    protected PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        FilterManager $filterManager,
        PaginatorInterface $paginator
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->filterManager = $filterManager;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     * @param object $object
     * @return FormInterface
     */
    public function postAction(Request $request, object $object): FormInterface
    {
        $form = $this->formFactory->create($this->getFormType(), $object);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $form;
    }

    /**
     * @param Request $request
     * @param object $object
     * @return FormInterface
     */
    public function putAction(Request $request, object $object): FormInterface
    {
        $content = json_decode($request->getContent(), true);
        $form = $this->formFactory->create($this->getFormType(), $object);
        $form->submit($content);

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $form;
    }

    /**
     * @param object $object
     */
    public function deleteAction(object $object): void
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }

    /** Returns form type handle by the actions */
    public function getFormType(): string
    {
        return BaseType::class;
    }
}