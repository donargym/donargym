<?php

namespace App\Form\Type;

use App\Entity\TrainingsstageTrainer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TrainingsstageTrainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'Naam*'))
            ->add('emailaddress', EmailType::class, array('label' => 'Email adres*'))
            ->add('phone1', TextType::class, array('label' => 'Eigen telefoonnummer*'))
            ->add('phone2', TextType::class, array('required' => true, 'label' => '2e telefoonnummer in geval van nood*'))
            ->add('huisArts', TextType::class, array('label' => 'Naam huisarts*'))
            ->add('insuranceCompany', TextType::class, array('label' => 'Verzekeringsmaatschappij*'))
            ->add('insuranceCard', FileType::class, array('label' => 'Kopie/screenshot verzekeringspasje (jpg, png of gif)*'))
            ->add(
                'diet',
                TextareaType::class,
                array('required' => false, 'label' => 'Dieet en overige opmerkingen',)
            )
            ->add('save', SubmitType::class, array('label' => 'Verstuur formulier'));
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
