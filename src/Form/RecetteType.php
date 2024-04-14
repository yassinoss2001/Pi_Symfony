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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
            'required' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le champ description est requis.',
                ]),
            ],
        ])
        ->add('ingredients', TextareaType::class, [
            'label' => 'Ingrédients',
            'attr' => ['rows' => 3], // Nombre de lignes dans le champ de texte
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Veuillez ajouter au moins un ingrédient.',
                ]),
            ],
        ])
        ->add('etape', TextareaType::class, [
            'label' => 'Étapes',
            'attr' => ['rows' => 5], // Nombre de lignes dans le champ de texte
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Veuillez ajouter au moins une étape.',
                ]),
            ],
        ])
        ->add('image', FileType::class, [
            'required' => false,
            'data_class' => null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez télécharger une image',
                ])
                ],
                ])
        ->add('video', FileType::class, [
            'required' => false,
            'data_class' => null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez télécharger une vidéo.',
                ])
                ],
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