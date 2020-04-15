<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriptionPaperFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label'       => 'subscription_paper_form_form.name',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])]
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label'       => 'subscription_paper_form_form.file',
                    'constraints' => [
                        New File(
                            [
                                'maxSize'        => '10M',
                                'maxSizeMessage' => 'subscription_paper_form_upload.max_size',
                            ]
                        )
                    ],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.upload']);
    }
}
