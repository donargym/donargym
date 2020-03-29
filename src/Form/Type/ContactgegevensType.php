<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactgegevensType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('straatnr', TextType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'Straat + Nr'
                ),
            ))
            ->add('postcode', TextType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'Postcode'
                ),
            ))
            ->add('plaats', TextType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'Plaats'
                    ),
            ))
            ->add('tel1', TextType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => '0612345678'
                    ),
            ))
            ->add('tel2', TextType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => '0612345678',
                    ),
                'required' => false
            ))
            ->add('tel3', TextType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => '0612345678'
                    ),
                'required' => false
            ))
            ->add('Verstuur', SubmitType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
        ));
    }

    public function getName()
    {
        return 'contactgegevens';
    }
}
