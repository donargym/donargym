<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoedselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('voedsel', TextType::class, array(
                'attr' => array(
                    'style' => 'width:300px',
                    'placeholder' => 'Eten/Drinken',
                    'label' => 'Wat voor eten/drinken?',
                ),
            ))
            ->add('hoeveelheid', TextType::class, array(
                'attr' => array(
                    'style' => 'width:300px',
                    'placeholder' => 'Hoeveelheid',
                    'label' => 'Hoeveel?',
                ),
            ))
            ->add('overig', TextType::class, array(
                'attr' => array(
                    'style' => 'width:300px',
                    'placeholder' => 'Opmerking',
                    'label' => 'Evt. Overige opmerkingen',
                ),
                'required' => false
            ))
            ->add('Verstuur', SubmitType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Voedsel',
        ));
    }

    public function getName()
    {
        return 'voedsel';
    }
}
