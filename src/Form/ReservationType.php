<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Gregwar\CaptchaBundle\Type\CaptchaType;
class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_reservation', null, [
                'constraints' => [
                    
                ],
            ])
            ->add('nombre_participants', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'integer']),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('email_contact', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('user_id', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                   
                ],
            ])
            ->add('evennement_id', null, [
                'constraints' => [
                    new Assert\NotBlank(),
            
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
