<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'required' => true,
                'placeholder' => 'choose.author',
                'help' => '<a href="/authors/create" class="text-indigo-400 text-sm hover:underline">** If you can\'t find it, add a new one</a>',
                'help_html' => true,
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'required' => true,
                'placeholder' =>  'choose.genre',
                'help' => '<a href="/genres/create" class="text-indigo-400 text-sm hover:underline">** If you can\'t find it, add a new one</a>',
                'help_html' => true,
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
            ])
            ->add('summary', TextareaType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
