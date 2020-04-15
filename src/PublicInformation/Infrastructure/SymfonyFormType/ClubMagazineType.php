<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ClubMagazineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'issueDate',
                DateType::class,
                [
                    'years'       => range(date('Y'), date('Y') - 10),
                    'format'      => 'dd MM yyyy',
                    'label'       => 'club_magazine_form.issueDate',
                    'input'       => 'datetime_immutable',
                    'constraints' => [
                        new NotBlank(['message' => 'general.not_blank']),
                        new Type(['type' => DateTimeImmutable::class, 'message' => 'general.date_time_type']),
                    ],
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label'       => 'club_magazine_form.file',
                    'constraints' => [
                        New File(
                            [
                                'maxSize'        => '10M',
                                'maxSizeMessage' => 'club_magazine_upload.max_size',
                            ]
                        )
                    ],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.upload']);
    }
}
