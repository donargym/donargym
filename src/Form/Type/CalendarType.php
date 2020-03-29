<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('datum', DateType::class, array(
                'widget' => 'single_text',
            ))
            ->add('activiteit', TextType::class)
            ->add('locatie', TextareaType::class, array(
                'attr' => array('cols' => '35', 'rows' => '7'),
                'required' => false,
            ))
            ->add('tijd', TextareaType::class, array(
                'attr' => array('cols' => '35', 'rows' => '7'),
                'required' => false,
            ))
            ->add('Verstuur', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Calendar',
        ));
    }

    public function getName()
    {
        return 'calendar';
    }
}
