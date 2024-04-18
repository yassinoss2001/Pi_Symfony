<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('idMenu', ChoiceType::class, [
                'choices' => [
                    'Menu Etudiant' => '1',
                    'Plat + Sandwich' => '2',
                    'Menu Royal' => '3',
                    
                ],
                
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un Menu']),
                ],
            ])
            ->add('adresse')
            ->add('longitude')
            ->add('latitude')
            ->add('modePayement', ChoiceType::class, [
                'choices' => [
                    'Cash' => 'Cash',
                    'Carte Bancaire' => 'Carte Bancaire',
                    
                    
                ],
                
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un Menu']),
                ],
            ])
            ->add('remarque')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
