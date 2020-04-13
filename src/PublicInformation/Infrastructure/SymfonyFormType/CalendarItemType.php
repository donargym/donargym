<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CalendarItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date',
                DateType::class,
                [
                    'data'        => $options['date'],
                    'years'       => range(date('Y'), 1908),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'calendar_form.date',
                    'input'       => 'datetime_immutable',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Type(['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type']),
                    ],
                ]
            )
            ->add(
                'activity',
                TextType::class,
                [
                    'data'        => $options['activity'],
                    'label'       => 'calendar_form.activity',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])],
                ],
                )
            ->add(
                'location',
                TextareaType::class,
                [
                    'data'     => $options['location'],
                    'label'    => 'calendar_form.location',
                    'attr'     => ['cols' => '35', 'rows' => '7'],
                    'required' => false,
                ]
            )
            ->add(
                'time',
                TextareaType::class,
                [
                    'data'     => $options['time'],
                    'label'    => 'calendar_form.time',
                    'attr'     => ['cols' => '35', 'rows' => '7'],
                    'required' => false,
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['date' => null, 'activity' => null, 'location' => null, 'time' => null]);
    }
}
