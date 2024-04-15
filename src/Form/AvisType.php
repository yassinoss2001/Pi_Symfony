<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
     
        ->add('note')
        ->add('commentaire')
        ->add('id_user', EntityType::class, [
            'class' => 'App\Entity\User', // Remplacez par le chemin correct vers votre entité User
            'choice_label' => 'nom', // Remplacez 'nom' par le champ que vous voulez afficher dans la liste déroulante
        ])
        ->add('id_recette', EntityType::class, [
            'class' => 'App\Entity\Recette', // Remplacez par le chemin correct vers votre entité Recette
            'choice_label' => 'titre', // Remplacez 'nomRecette' par le champ que vous voulez afficher dans la liste déroulante
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
