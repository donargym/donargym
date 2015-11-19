<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoedselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('voedsel', 'text', array(
                'attr' => array(
                    'style' => 'width:300px',
                    'placeholder' => 'Eten/Drinken',
                    'label' => 'Wat voor eten/drinken?',
                ),
            ))
            ->add('hoeveelheid', 'text', array(
                'attr' => array(
                    'style' => 'width:300px',
                    'placeholder' => 'Hoeveelheid',
                    'label' => 'Hoeveel?',
                ),
            ))
            ->add('overig', 'text', array(
                'attr' => array(
                    'style' => 'width:300px',
                    'placeholder' => 'Opmerking',
                    'label' => 'Evt. Overige opmerkingen',
                ),
                'required' => false
            ))
            ->add('Verstuur', 'submit')
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Voedsel',
        ));
    }

    public function getName()
    {
        return 'voedsel';
    }
}