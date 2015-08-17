<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VeelgesteldeVragenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vraag', 'text', array(
                'attr' => array('style' => 'width:250px'),
            ))
            ->add('antwoord', 'textarea', array(
                'attr' => array('cols' => '35', 'rows' => '8'),
            ))
            ->add('Verstuur', 'submit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VeelgesteldeVragen',
        ));
    }

    public function getName()
    {
        return 'veelgesteldevragen';
    }
}