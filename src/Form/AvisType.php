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
    ->add('commentaire')
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
