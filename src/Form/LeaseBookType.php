<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LeaseBookType
 * Used to handle the book leasing process
 *
 * @package App\Form
 */
class LeaseBookType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reader', EntityType::class, [
                'class' => Reader::class,
                'choice_label' => 'name',
            ])
            ->add('returnDate', DateType::class, [
                'widget' => 'single_text',
            ])
        ;

        /**
         * Check - before data to be set to the object - if return date is higher than the current date,
         * the book is not available to be leased
         */
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Book $book */
            $book = $event->getData();
            $form = $event->getForm();

            $oldBook = $this->entityManager->getUnitOfWork()->getOriginalEntityData($book);
            $returnDate = $oldBook['returnDate'];

            if ($returnDate instanceof \DateTime && $returnDate > new \DateTime()) {
                $form->addError(new FormError('This book is not available for leasing.'));
            }
        });
    }
}