<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimplePageContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'pageContent',
                TextareaType::class,
                ['data'  => $options['content'],
                 'attr'  => ['cols' => '80', 'rows' => '40'],
                 'label' => 'simple_content_page_form.content'
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['content' => null]);
    }
}
