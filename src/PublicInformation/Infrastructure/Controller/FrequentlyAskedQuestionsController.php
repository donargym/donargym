<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalFrequentlyAskedQuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class FrequentlyAskedQuestionsController
{
    private Environment $twig;
    private DbalFrequentlyAskedQuestionRepository $frequentlyAskedQuestionRepository;

    public function __construct(
        Environment $twig,
        DbalFrequentlyAskedQuestionRepository $frequentlyAskedQuestionRepository
    )
    {
        $this->twig                              = $twig;
        $this->frequentlyAskedQuestionRepository = $frequentlyAskedQuestionRepository;
    }

    /**
     * @Route("/contact/veelgestelde-vragen/", name="frequentlyAskedQuestions", methods={"GET"})
     */
    public function frequentlyAskedQuestions(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/contact/frequently_asked_questions.html.twig',
                ['questions' => $this->frequentlyAskedQuestionRepository->findAll()]
            )
        );
    }
}
