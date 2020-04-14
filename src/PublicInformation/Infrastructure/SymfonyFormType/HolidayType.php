<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'data'        => $options['name'],
                    'label'       => 'holiday_form.name',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])],
                ],
                )
            ->add(
                'startDate',
                DateType::class,
                [
                    'data'        => $options['startDate'],
                    'years'       => range(date('Y'), date('Y') + 5),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'holiday_form.start_date',
                    'input'       => 'datetime_immutable',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Type(['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type']),
                    ],
                ]
            )
            ->add(
                'endDate',
                DateType::class,
                [
                    'data'        => $options['endDate'],
                    'years'       => range(date('Y'), date('Y') + 5),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'holiday_form.end_date',
                    'input'       => 'datetime_immutable',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Type(['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type']),
                    ],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['name' => null, 'startDate' => null, 'endDate' => null]);
    }
}
