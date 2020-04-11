<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalNewsPostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class NewsController
{
    private DbalNewsPostRepository $newsPostRepository;
    private Environment            $twig;

    public function __construct(
        DbalNewsPostRepository $newsPostRepository,
        Environment $twig
    ) {
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
                '@PublicInformation/default/news.html.twig',
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
                '@PublicInformation/default/archive_index.html.twig',
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
                '@PublicInformation/default/news.html.twig',
                ['newPosts' => $this->newsPostRepository->findNewsPostsForYear($year)]
            )
        );
    }

    /**
     * @Route("/news/", name="removeNewsPost", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function removeNewsPost(Request $request): Response
    {
        $this->newsPostRepository->remove((int) $request->request->get('id'));

        return new RedirectResponse($request->headers->get('referer'));
    }
}
