<?php

declare(strict_types=1);

namespace App\Infrastructure\SymfonyController;

use App\Infrastructure\DoctrineDbal\DbalAboutGymnastRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionGroupMemberRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionGroupRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionResultRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class SymfonyPublicCompetitionGroupController
{
    private DbalCompetitionGroupRepository $competitionGroupRepository;
    private DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository;
    private DbalCompetitionResultRepository $competitionResultRepository;
    private DbalAboutGymnastRepository $aboutGymnastRepository;
    private Environment $twig;

    public function __construct(
        DbalCompetitionGroupRepository $competitionGroupRepository,
        DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository,
        DbalCompetitionResultRepository $competitionResultRepository,
        DbalAboutGymnastRepository $aboutGymnastRepository,
        Environment $twig
    )
    {
        $this->competitionGroupRepository       = $competitionGroupRepository;
        $this->competitionGroupMemberRepository = $competitionGroupMemberRepository;
        $this->competitionResultRepository      = $competitionResultRepository;
        $this->aboutGymnastRepository           = $aboutGymnastRepository;
        $this->twig                             = $twig;
    }

    /**
     * @Route("/wedstrijdturnen/{groupId}", name="showCompetitionGroup", methods={"GET"})
     */
    public function showCompetitionGroup(int $groupId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find($groupId);
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                'wedstrijdturnen/competition_group.html.twig',
                [
                    'group'                   => $competitionGroup,
                    'competitionGroupMembers' => $this->competitionGroupMemberRepository->findAllForGroup($groupId),
                ]
            )
        );
    }

    /**
     * @Route("/wedstrijdturnen/{groupId}/wedstrijduitslagen", name="competitionResults", methods={"GET"})
     */
    public function competitionResults(int $groupId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find($groupId);
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                'wedstrijdturnen/competition_results.html.twig',
                [
                    'group'              => $competitionGroup,
                    'competitionResults' => $this->competitionResultRepository->findAllForGroup($groupId),
                ]
            )
        );
    }

    /**
     * @Route("/wedstrijdturnen/{groupId}/turnster/{gymnastId}", name="aboutGymnastPage", methods={"GET"})
     */
    public function aboutGymnastPage(int $groupId, int $gymnastId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find($groupId);
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }

        $gymnast = $this->competitionGroupMemberRepository->find($gymnastId);
        if (!$gymnast) {
            throw new NotFoundHttpException();
        }

        $aboutGymnast = $this->aboutGymnastRepository->findForGymnast($gymnastId);
        if (!$aboutGymnast) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                'wedstrijdturnen/about_gymnast.html.twig',
                [
                    'group'        => $competitionGroup,
                    'aboutGymnast' => $aboutGymnast,
                    'gymnast'      => $gymnast,
                ]
            )
        );
    }
}
