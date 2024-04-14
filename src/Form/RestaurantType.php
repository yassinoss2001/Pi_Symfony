<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idCategorie', NumberType::class, [
            'label' => 'IdCategorie',
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le nom du produit doit contenir au moins une lettre.',
                ]),
            ],
        ])
        ->add('speciality', TextType::class, [
            'label' => 'Speciality',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s,]+$/',
                    'message' => 'La specialite ne peuvent contenir que des lettres, des chiffres et des virgules.',
                ]),
            ],
        ])
        ->add('telephone', TelType::class, [
            'label' => 'Telephone',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[0-9]+$/',
                    'message' => 'Le telephone ne peut contenir que des chiffres',
                ]),
        ],
        ])

        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s,]+$/',
                    'message' => 'La description ne peuvent contenir que des lettres, des chiffres et des virgules.',
                ]),
        ],
        ])
        ->add('place', TextType::class, [
            'label' => 'Place',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s,]+$/',
                    'message' => 'Le lieu ne peuvent contenir que des lettres, des chiffres et des virgules.',
                ]),
        ],
        ])
        ->add('rate', TextType::class, [
            'label' => 'Rate',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^[0-9]+$/',
                    'message' => 'Le rate ne peuvent contenir que  des chiffres ',
                ]),
        ],
        ])
        ->add('image', FileType::class, [
            'label' => 'Image',
            'required' => false,
            'data_class' => null, // Update to handle file uploads properly
        ]);
}
        
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
