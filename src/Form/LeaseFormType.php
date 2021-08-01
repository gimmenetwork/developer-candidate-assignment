<?php

namespace App\Form;

use App\DataTransformer\BookIdTransformer;
use App\Entity\Reader;
use App\Entity\ReaderBook;
use App\Validator\LeaseBook;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class LeaseFormType extends AbstractType
{
    private BookIdTransformer $transformer;

    public function __construct(BookIdTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reader',EntityType::class,  [
                'class' => Reader::class,
                'placeholder' => 'choose.reader',
                'constraints' => [new LeaseBook()],
            ])
            ->add('book', HiddenType::class, [
                'invalid_message' => 'That is not a valid book id',
            ])
            ->add('returnAt', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
        ;

        $builder->get('book')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReaderBook::class,
            'attr' => [
                'class' => 'text-left',
            ],
        ]);
    }
}
