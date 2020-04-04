<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\SymfonyController;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalClubMagazineRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class SymfonyClubMagazineController
{
    private DbalClubMagazineRepository $clubMagazineRepository;
    private Environment $twig;

    public function __construct(
        DbalClubMagazineRepository $clubMagazineRepository,
        Environment $twig
    )
    {
        $this->clubMagazineRepository = $clubMagazineRepository;
        $this->twig                   = $twig;
    }

    /**
     * @Route("/clubblad", name="clubMagazine", methods={"GET"})
     */
    public function clubMagazine(): Response
    {
        return new Response(
            $this->twig->render(
                '@PublicInformation/default/club_magazine.html.twig',
                [
                    'clubMagazines' => $this->clubMagazineRepository->findAll(),
                    'years'         => $this->clubMagazineRepository->findAllYears(),
                ]
            )
        );
    }
}
