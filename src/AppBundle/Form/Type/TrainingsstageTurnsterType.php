<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\TrainingsstageTurnster;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TrainingsstageTurnsterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'Naam turnster*'))
            ->add(
                'dateofbirth',
                'date',
                array(
                    'empty_value' => '',
                    'years'       => range(1980, date('Y')),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'Geboortedatum*'
                )
            )
            ->add('emailaddress', 'email', array('label' => 'Email adres*'))
            ->add('phone1', 'text', array('label' => 'Telefoonnummer*'))
            ->add('phone2', 'text', array('required' => false, 'label' => 'Evt. 2e telefoonnummer'))
            ->add('huisArts', 'text', array('label' => 'Naam huisarts*'))
            ->add('insuranceCompany', 'text', array('label' => 'Verzekeringsmaatschappij*'))
            ->add('insuranceCard', 'file', array('label' => 'Kopie verzekeringspasje (jpg, png of gif)*'))
            ->add('bankaccountholder', 'text', array('label' => 'Naam rekeninghouder vanaf waar de betaling gedaan wordt. Betalen kan via '))
            ->add('diet', 'textarea', array('required' => false, 'label' => 'Allergisch voor of lust/mag ABSOLUUT geen...',))
            ->add('medicines', 'textarea', array('required' => false, 'label' => 'Medicijngebruik; naam medicatie, frequentie van inname en of uw kind het zelf kan innemen of hulp nodig heeft',))
            ->add('other', 'textarea', array('required' => false, 'label' => 'Bijzonderheden zoals slaapwandelen, heimwee, graag op de kamer bij... etc',))
            ->add(
                'accept',
                'checkbox',
                array(
                    'required' => true,
                    'label'    => 'Hierbij geef ik mijn dochter op om mee te gaan op trainingsstage. Ik weet dat wanneer ik dit formulier verzend de inschrijving definitief is en ik €95 dien te betalen ook als mijn dochter onverhoopt niet mee kan op trainingsstage.',
                )
            )
            ->add('save', 'submit', array('label' => 'Verstuur formulier'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => TrainingsstageTurnster::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trainingsstage';
    }
}
