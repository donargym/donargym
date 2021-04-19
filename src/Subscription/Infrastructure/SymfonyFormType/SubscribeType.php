<?php
declare(strict_types=1);

namespace App\Subscription\Infrastructure\SymfonyFormType;

use App\Subscription\Domain\TrainerOptions;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

final class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'firstName',
            TextType::class,
            [
                'label'       => 'subscription_form.first_name',
                'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
            ]
        )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label'       => 'subscription_form.last_name',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'nameLetters',
                TextType::class,
                [
                    'label'       => 'subscription_form.initials',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'dateOfBirth',
                DateType::class,
                [
                    'empty_data'  => '',
                    'years'       => range(date('Y'), 1908),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'subscription_form.date_of_birth',
                    'input'       => 'datetime_immutable',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Type(['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type'])
                    ],
                ]
            )
            ->add(
                'gender',
                ChoiceType::class,
                [
                    'label'       => 'subscription_form.gender',
                    'placeholder' => 'subscription_form.gender_place_holder',
                    'choices'     => [
                        'subscription_form.gender_male'   => 'Man',
                        'subscription_form.gender_female' => 'Vrouw',
                    ],
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label'       => 'subscription_form.street_house_number',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'postcode',
                TextType::class,
                [
                    'label'       => 'subscription_form.zip_code',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label'       => 'subscription_form.city',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'phone1',
                TextType::class,
                [
                    'label'       => 'subscription_form.phone_1',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add('phone2', TextType::class, ['required' => false, 'label' => 'subscription_form.phone_2'])
            ->add(
                'bankAccountNumber',
                TextType::class,
                [
                    'label'       => 'subscription_form.iban',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Iban(['message' => 'general.iban'])
                    ]
                ]
            )
            ->add(
                'bankAccountHolder',
                TextType::class,
                [
                    'label'       => 'subscription_form.account_holder',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'emailAddress',
                EmailType::class,
                [
                    'label'       => 'subscription_form.email',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Email(['message' => 'general.email'])
                    ]
                ]
            )
            ->add(
                'ooievaarspas',
                ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.ooievaarspas',
                    'choices'     => [
                        'form.yes' => 'Ja',
                        'form.no'  => 'Nee',
                    ],
                    'expanded'    => true,
                    'constraints' => [new NotNull(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'haveBeenSubscribed',
                ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.have_been_subscribed',
                    'choices'     => [
                        'form.yes' => 'Ja',
                        'form.no'  => 'Nee',
                    ],
                    'expanded'    => true,
                    'constraints' => [new NotNull(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'subscribedFrom',
                DateType::class,
                [
                    'format'      => 'dd MM yyyy',
                    'required'    => false,
                    'label'       => 'subscription_form.subscribed_from',
                    'years'       => range(1908, date('Y')),
                    'input'       => 'datetime_immutable',
                    'constraints' => new Type(
                        ['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type']
                    )
                ]
            )
            ->add(
                'subscribedUntil',
                DateType::class,
                [
                    'format'      => 'dd MM yyyy',
                    'required'    => false,
                    'label'       => 'subscription_form.subscribed_until',
                    'years'       => range(1908, date('Y')),
                    'input'       => 'datetime_immutable',
                    'constraints' => new Type(
                        ['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type']
                    )
                ]
            )
            ->add(
                'otherClub',
                ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.other_club',
                    'choices'     => [
                        'form.yes' => 'Ja',
                        'form.no'  => 'Nee',
                    ],
                    'expanded'    => true,
                    'constraints' => [new NotNull(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'whatOtherClub',
                TextType::class,
                ['required' => false, 'label' => 'subscription_form.what_other_club']
            )
            ->add(
                'paidBondContribution',
                ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.paid_bond_contribution',
                    'choices'     => [
                        'form.yes' => 'Ja',
                        'form.no'  => 'Nee',
                    ],
                    'expanded'    => true,
                    'constraints' => [new NotNull(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label'       => 'subscription_form.category',
                    'placeholder' => 'subscription_form.category_place_holder',
                    'choices'     => [
                        'subscription_form.tot'                   => 'Peuters t/m 3 jaar',
                        'subscription_form.toddlers'              => 'Kleuters 4 t/m 6 jaar',
                        'subscription_form.girls_6-9'             => 'Meisjes 6 t/m 9 jaar',
                        'subscription_form.girls_10-15'           => 'Meisjes 10 t/m 15 jaar',
                        'subscription_form.boys_6-9'              => 'Jongens 6 t/m 9 jaar',
                        'subscription_form.boys_10-15'            => 'Jongens 10 t/m 15 jaar',
                        'subscription_form.ladies_above_16'       => 'Dames 16 jaar en ouder',
                        'subscription_form.gentleman_above_16'    => 'Heren 16 jaar en ouder',
                        'subscription_form.aerobics_body_shape'   => 'Aerobics/Bodyshape',
                        'subscription_form.badminton_volley_ball' => 'Badminton/Volleybal',
                    ],
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'days',
                ChoiceType::class,
                [
                    'label'       => 'subscription_form.days',
                    'multiple'    => true,
                    'expanded'    => true,
                    'choices'     => [
                        'weekday.monday'    => 'Maandag',
                        'weekday.tuesday'   => 'Dinsdag',
                        'weekday.wednesday' => 'Woensdag',
                        'weekday.thursday'  => 'Donderdag',
                        'weekday.friday'    => 'Vrijdag',
                    ],
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'locations',
                ChoiceType::class,
                [
                    'label'       => 'subscription_form.location',
                    'multiple'    => true,
                    'expanded'    => true,
                    'choices'     => [
                        'subscription_form.location_1' => 'Mari Andriessenstraat',
                        'subscription_form.location_2' => 'Zaanstraat',
                        'subscription_form.location_3' => 'Renswoudelaan',
                        'subscription_form.location_4' => '2e Sweelinckstraat',
                        'subscription_form.location_5' => 'Den Helderstraat',
                        'subscription_form.location_6' => 'Erasmusweg',
                        'subscription_form.location_7' => 'Walenburg',
                        'subscription_form.location_8' => 'Sportcampus Zuiderpark',
                    ],
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'startTime',
                TimeType::class,
                [
                    'empty_data'  => '',
                    'label'       => 'subscription_form.start_time',
                    'input'       => 'datetime_immutable',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'trainer',
                ChoiceType::class,
                [
                    'label'       => 'subscription_form.trainer',
                    'placeholder' => 'subscription_form.trainer_place_holder',
                    'choices'     => TrainerOptions::trainerOptionsForForm(),
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'how',
                TextareaType::class,
                ['required' => false, 'label' => 'subscription_form.how']
            )
            ->add(
                'voluntaryWork',
                TextareaType::class,
                [
                    'attr'        => ['rows' => '4', 'cols' => '50'],
                    'required'    => true,
                    'label'       => 'subscription_form.voluntary_work',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'accept',
                CheckboxType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.accept_requirements',
                    'constraints' => [new IsTrue(['message' => 'general.required_to_agree'])]
                ]
            )
            ->add(
                'acceptPrivacyPolicy',
                CheckboxType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.accept_privacy_policy',
                    'constraints' => [new IsTrue(['message' => 'general.required_to_agree'])]
                ]
            )
            ->add(
                'acceptNamePublished',
                ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.accept_name_published',
                    'choices'     => [
                        'form.yes' => true,
                        'form.no'  => false,
                    ],
                    'expanded'    => true,
                    'constraints' => [new NotNull(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'acceptPicturesPublished',
                ChoiceType::class,
                [
                    'required'    => true,
                    'label'       => 'subscription_form.accept_picture_published',
                    'choices'     => [
                        'form.yes' => true,
                        'form.no'  => false,
                    ],
                    'expanded'    => true,
                    'constraints' => [new NotNull(['message' => 'general.not_blank'])]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.submit']);
    }
}
