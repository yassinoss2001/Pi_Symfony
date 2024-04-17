<?php

namespace App\Form;

use App\Entity\Evennemnt;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvennemntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event')
            ->add('desc_event')
            ->add('date_debut')
            ->add('date_fin')
            ->add('lieu_evenement')
            ->add('Nbr_participants')
            ->add('Time_debut')
            ->add('Time_fin')
            ->add('NameResto')
            ->add('image_path')
            ->add('user_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evennemnt::class,
        ]);
    }
}
