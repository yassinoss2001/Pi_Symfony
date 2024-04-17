<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
     
    ->add('note', IntegerType::class, [
        'constraints' => [
            new Assert\NotBlank(),
            new Assert\Range([
                'min' => 1,
                'max' => 5,
                'minMessage' => 'La note doit être d\'au moins 1.',
                'maxMessage' => 'La note ne peut pas dépasser 5.',
            ]),
        ],
    ])
    ->add('commentaire', null, [
        'constraints' => [
            new Assert\NotBlank([
                'message' => 'Le commentaire ne peut pas être vide.',
            ]),
            new Assert\Length([
                'min' => 5,
                'max' => 1000,
                'minMessage' => 'Le commentaire doit contenir au moins {{ limit }} caractères.',
                'maxMessage' => 'Le commentaire ne peut pas dépasser {{ limit }} caractères.',
            ]),
        ],
    ])
        ->add('id_user', EntityType::class, [
            'class' => 'App\Entity\User', // Remplacez par le chemin correct vers votre entité User
            'choice_label' => 'nom', // Remplacez 'nom' par le champ que vous voulez afficher dans la liste déroulante
        ])
       
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
