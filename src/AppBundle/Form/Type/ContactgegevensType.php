<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactgegevensType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('straatnr', 'text', array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'Straat + Nr'
                ),
            ))
            ->add('postcode', 'text', array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'Postcode'
                ),
            ))
            ->add('plaats', 'text', array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'Plaats'
                    ),
            ))
            ->add('tel1', 'text', array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => '0612345678'
                    ),
            ))
            ->add('tel2', 'text', array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => '0612345678',
                    ),
                'required' => false
            ))
            ->add('tel3', 'text', array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => '0612345678'
                    ),
                'required' => false
            ))
            ->add('Verstuur', 'submit')
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'contactgegevens';
    }
}