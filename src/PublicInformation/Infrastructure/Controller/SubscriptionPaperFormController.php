<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalSubscriptionPaperFormRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class SubscriptionPaperFormController
{
    private DbalSubscriptionPaperFormRepository $subscriptionPaperFormRepository;
    private Environment $twig;

    public function __construct(
        DbalSubscriptionPaperFormRepository $subscriptionPaperFormRepository,
        Environment $twig
    )
    {
        $this->subscriptionPaperFormRepository = $subscriptionPaperFormRepository;
        $this->twig                            = $twig;
    }

    /**
     * @Route("/lidmaatschap/formulieren/", name="subscriptionPaperForms", methods={"GET"})
     */
    public function subscriptionPaperForms(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/subscription/paper_forms.html.twig',
                ['subscriptionPaperForms' => $this->subscriptionPaperFormRepository->findAll()]
            )
        );
    }
}
