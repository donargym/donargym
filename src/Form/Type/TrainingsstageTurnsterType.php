<?php

namespace App\Form\Type;

use App\Entity\TrainingsstageTurnster;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TrainingsstageTurnsterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'Naam turnster*'))
            ->add(
                'dateofbirth',
                DateType::class,
                array(
                    'empty_value' => '',
                    'years'       => range(1980, date('Y')),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'Geboortedatum*'
                )
            )
            ->add('emailaddress', EmailType::class, array('label' => 'Email adres*'))
            ->add('phone1', TextType::class, array('label' => 'Telefoonnummer*'))
            ->add('phone2', TextType::class, array('required' => false, 'label' => 'Evt. 2e telefoonnummer'))
            ->add('huisArts', TextType::class, array('label' => 'Naam huisarts*'))
            ->add('insuranceCompany', TextType::class, array('label' => 'Verzekeringsmaatschappij*'))
            ->add('insuranceCard', FileType::class, array('label' => 'Kopie/screenshot verzekeringspasje (jpg, png of gif)*'))
            ->add('bankaccountholder', TextType::class, array('label' => 'Naam rekeninghouder vanaf waar de betaling gedaan wordt. Betalen kan via '))
            ->add('diet', TextareaType::class, array('required' => false, 'label' => 'Allergisch voor of lust/mag ABSOLUUT geen...',))
            ->add('medicines', TextareaType::class, array('required' => false, 'label' => 'Medicijngebruik; naam medicatie, frequentie van inname en of uw kind het zelf kan innemen of hulp nodig heeft',))
            ->add('other', TextareaType::class, array('required' => false, 'label' => 'Bijzonderheden zoals slaapwandelen, heimwee, graag op de kamer bij... etc',))
            ->add(
                'accept',
                CheckboxType::class,
                array(
                    'required' => true,
                    'label'    => 'Hierbij geef ik mijn dochter op om mee te gaan op trainingsstage. Ik weet dat wanneer ik dit formulier verzend de inschrijving definitief is en ik €95 dien te betalen ook als mijn dochter onverhoopt niet mee kan op trainingsstage.',
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Verstuur formulier'));
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
