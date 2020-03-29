<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VakantiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('naam', TextType::class, array(
                'attr' => array('style' => 'width:140px'),
            ))
            ->add('van', DateType::class, array(
                'widget' => 'single_text',
            ))
            ->add('tot', DateType::class, array(
                'widget' => 'single_text',
            ))
            ->add('Verstuur', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Vakanties',
        ));
    }

    public function getName()
    {
        return 'vakanties';
    }
}
