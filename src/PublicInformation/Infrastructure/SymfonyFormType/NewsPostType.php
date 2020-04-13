<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'data'        => $options['title'],
                    'label'       => 'news_post_form.title',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])],
                ],
                )
            ->add(
                'content',
                TextareaType::class,
                [
                    'data'        => $options['content'],
                    'label'       => 'news_post_form.content',
                    'attr'        => ['cols' => '35', 'rows' => '7'],
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['title' => null, 'content' => null]);
    }
}
