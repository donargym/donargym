<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\SymfonyController;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalNewsPostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class SymfonyNewsController
{
    private DbalNewsPostRepository $newsPostRepository;
    private Environment $twig;

    public function __construct(
        DbalNewsPostRepository $newsPostRepository,
        Environment $twig
    )
    {
        $this->newsPostRepository = $newsPostRepository;
        $this->twig               = $twig;
    }

    /**
     * @Route("/", name="newsPosts", methods={"GET"})
     */
    public function newsPosts(): Response
    {
        return new Response(
            $this->twig->render(
                'default/news.html.twig',
                ['newPosts' => $this->newsPostRepository->findTenMostRecentNewsPosts()]
            )
        );
    }

    /**
     * @Route("/archief", name="newsArchiveIndex", methods={"GET"})
     */
    public function newsArchiveIndex(): Response
    {
        return new Response(
            $this->twig->render(
                'default/archive_index.html.twig',
                ['years' => $this->newsPostRepository->findYearsForArchive()]
            )
        );
    }

    /**
     * @Route("/archief/{year}", name="newsArchiveForYear", methods={"GET"})
     */
    public function newsArchiveForYear(int $year): Response
    {
        return new Response(
            $this->twig->render(
                'default/news.html.twig',
                ['newPosts' => $this->newsPostRepository->findNewsPostsForYear($year)]
            )
        );
    }
}
