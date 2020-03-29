<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Email2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email2', EmailType::class, array(
                'attr' => array(
                    'style' => 'width:200px',
                    'placeholder' => 'voorbeeld@voorbeeld.nl'
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
        return 'email2';
    }
}
