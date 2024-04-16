<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints as Assert;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', TextType::class, [
            'label' => 'titre',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le nom du produit doit contenir au moins une lettre.',
                ]),
            ],
        ])
        ->add('description', TextType::class, [
            'label' => 'description',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])
        ->add('ingredients', TextType::class, [
            'label' => 'ingredients',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s,]+$/',
                    'message' => 'Les ingrédients ne peuvent contenir que des lettres, des chiffres et des virgules.',
                ]),
            ],
        ])
        ->add('etape', TextType::class, [
            'label' => 'step',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s.]+$/',
                    'message' => 'Les étapes ne peuvent contenir que des lettres, des chiffres et des points.',
                ]),
            ],
        ])
        ->add('image', FileType::class, [
            'required' => false,
            'data_class' => null, // Update to handle file uploads properly
        ])
        ->add('video', FileType::class, [
            'required' => false,
            'data_class' => null, // Update to handle file uploads properly
        ])
        ->add('idUser', EntityType::class, [
            'class' => 'App\Entity\User',
            'choice_label' => 'nom', // Replace 'username' with the actual property you want to display
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}