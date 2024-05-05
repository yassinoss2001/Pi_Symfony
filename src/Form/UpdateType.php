<?php

namespace App\Form\User;

use App\Entity\User;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Client' => 'CLIENT',
                    'Livreur' => 'LIVREUR',
                    'Gérant' => 'GERANT',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit',SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
