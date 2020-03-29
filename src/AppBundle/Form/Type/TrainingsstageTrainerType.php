<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\TrainingsstageTrainer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TrainingsstageTrainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'Naam*'))
            ->add('emailaddress', 'email', array('label' => 'Email adres*'))
            ->add('phone1', 'text', array('label' => 'Eigen telefoonnummer*'))
            ->add('phone2', 'text', array('required' => true, 'label' => '2e telefoonnummer in geval van nood*'))
            ->add('huisArts', 'text', array('label' => 'Naam huisarts*'))
            ->add('insuranceCompany', 'text', array('label' => 'Verzekeringsmaatschappij*'))
            ->add('insuranceCard', 'file', array('label' => 'Kopie/screenshot verzekeringspasje (jpg, png of gif)*'))
            ->add(
                'diet',
                'textarea',
                array('required' => false, 'label' => 'Dieet en overige opmerkingen',)
            )
            ->add('save', 'submit', array('label' => 'Verstuur formulier'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => TrainingsstageTrainer::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trainingsstageTrainer';
    }
}
