<?php

namespace App\Form;

use App\Entity\Evennemnt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EvennemntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('desc_event', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('date_debut', null, [
                'constraints' => [
                   
                  
                ],
            ])
            ->add('date_fin', null, [
                'constraints' => [

                    //new Assert\GreaterThanOrEqual([
                      //  'propertyPath' => 'parent.all.date_debut',
                        //'message' => 'The end date must be later than the start date.',
                 //   ]),
                ],
            ])
            ->add('lieu_evenement', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('Nbr_participants', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'integer']),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('Time_debut', null, [
                'constraints' => [
                  
                    
                ],
            ])
            ->add('Time_fin', null, [
                'constraints' => [
                  
                    
                ],
            ])
            ->add('NameResto', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('image_path', FileType::class, [
                'label' => 'image_path',
                'mapped' => false, // This means the image field is not mapped to any entity property
                'required' => false, // Set to true if the field is required
            ])
            ->add('user_id', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                   
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evennemnt::class,
        ]);
    }
}
