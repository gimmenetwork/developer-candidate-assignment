<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'genre_list' => null,
            'attr' => ['class' => 'myForm']
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ["required"=>false])
            ->add('author', TextType::class, ["required"=>false])
            ->add('genre', ChoiceType::class, [
                'choices' => array_merge(["Select Genre" => ""], $options['genre_list']),
                'required' => false
            ])
            ->add('filter', SubmitType::class);
    }
}
