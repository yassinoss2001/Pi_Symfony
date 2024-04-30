<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ nom ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('prenom', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ prénom ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('email', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ email ne peut pas être vide.',
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.',
                        'mode' => 'strict',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ mot de passe ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ de confirmation du mot de passe ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'CLIENT' => 'CLIENT',
                    'LIVREUR' => 'LIVREUR',
                    'GERANT' => 'GERANT',
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
