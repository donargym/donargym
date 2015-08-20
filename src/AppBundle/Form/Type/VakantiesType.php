<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VakantiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('naam', 'text', array(
                'attr' => array('style' => 'width:140px'),
            ))
            ->add('van', 'date', array(
                'widget' => 'single_text',
            ))
            ->add('tot', 'date', array(
                'widget' => 'single_text',
            ))
            ->add('Verstuur', 'submit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Vakanties',
        ));
    }

    public function getName()
    {
        return 'vakanties';
    }
}