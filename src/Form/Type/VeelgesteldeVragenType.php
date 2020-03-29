<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VeelgesteldeVragenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vraag', TextType::class, array(
                'attr' => array('style' => 'width:250px'),
            ))
            ->add('antwoord', TextareaType::class, array(
                'attr' => array('cols' => '35', 'rows' => '8'),
            ))
            ->add('Verstuur', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\VeelgesteldeVragen',
        ));
    }

    public function getName()
    {
        return 'veelgesteldevragen';
    }
}
