<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('datum', 'date', array(
                'widget' => 'single_text',
            ))
            ->add('activiteit', 'text')
            ->add('locatie', 'textarea', array(
                'attr' => array('cols' => '35', 'rows' => '7'),
                'required' => false,
            ))
            ->add('tijd', 'textarea', array(
                'attr' => array('cols' => '35', 'rows' => '7'),
                'required' => false,
            ))
            ->add('Verstuur', 'submit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Calendar',
        ));
    }

    public function getName()
    {
        return 'calendar';
    }
}