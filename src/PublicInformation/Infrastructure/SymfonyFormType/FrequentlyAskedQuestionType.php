<?php

namespace App\PublicInformation\Infrastructure\SymfonyFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FrequentlyAskedQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'question',
                TextType::class,
                [
                    'data'        => $options['question'],
                    'label'       => 'frequently_asked_question_form.question',
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])],
                ],
                )
            ->add(
                'answer',
                TextareaType::class,
                [
                    'data'        => $options['answer'],
                    'label'       => 'frequently_asked_question_form.answer',
                    'attr'        => ['cols' => '35', 'rows' => '7'],
                    'constraints' => [new NotBlank(['message' => 'general.not_blank'])],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['question' => null, 'answer' => null]);
    }
}
