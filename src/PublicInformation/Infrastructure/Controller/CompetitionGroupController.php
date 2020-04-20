<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\Controller;

use App\PublicInformation\Infrastructure\DoctrineDbal\DbalAboutCompetitionGroupMemberRepository;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalCompetitionGroupMemberRepository;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalCompetitionGroupRepository;
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalCompetitionGroupCompetitionResultRepository;
use App\Shared\Domain\CompetitionGroupId;
use App\Shared\Domain\CompetitionGroupMemberId;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class CompetitionGroupController
{
    private DbalCompetitionGroupRepository                      $competitionGroupRepository;
    private DbalCompetitionGroupMemberRepository                $competitionGroupMemberRepository;
    private DbalCompetitionGroupCompetitionResultRepository                     $competitionResultRepository;
    private DbalAboutCompetitionGroupMemberRepository           $aboutGymnastRepository;
    private Environment                                         $twig;

    public function __construct(
        DbalCompetitionGroupRepository $competitionGroupRepository,
        DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository,
        DbalCompetitionGroupCompetitionResultRepository $competitionResultRepository,
        DbalAboutCompetitionGroupMemberRepository $aboutGymnastRepository,
        Environment $twig
    ) {
        $this->competitionGroupRepository       = $competitionGroupRepository;
        $this->competitionGroupMemberRepository = $competitionGroupMemberRepository;
        $this->competitionResultRepository      = $competitionResultRepository;
        $this->aboutGymnastRepository           = $aboutGymnastRepository;
        $this->twig                             = $twig;
    }

    /**
     * @Route("/wedstrijdturnen/{groupId}", name="showCompetitionGroup", methods={"GET"})
     */
    public function showCompetitionGroup(string $groupId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find(CompetitionGroupId::fromString($groupId));
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/competitive_gymnastics/competition_group.html.twig',
                [
                    'group'                   => $competitionGroup,
                    'competitionGroupMembers' => $this->competitionGroupMemberRepository->findAllForGroup(
                        CompetitionGroupId::fromString($groupId)
                    ),
                ]
            )
        );
    }

    /**
     * @Route("/wedstrijdturnen/{groupId}/wedstrijduitslagen", name="competitionResults", methods={"GET"})
     */
    public function competitionResults(string $groupId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find(CompetitionGroupId::fromString($groupId));
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                '@PublicInformation/competitive_gymnastics/competition_results.html.twig',
                [
                    'group' => $competitionGroup,
                    'competitionResults' => $this->competitionResultRepository->findAllForGroup(
                        CompetitionGroupId::fromString($groupId)
                    ),
                ]
            )
        );
    }

    /**
     * @Route("/wedstrijdturnen/{groupId}/turnster/{competitionGroupMemberId}", name="aboutCompetitionGroupMemberPage", methods={"GET"})
     */
    public function aboutCompetitionGroupMemberPage(string $groupId, string $competitionGroupMemberId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find(CompetitionGroupId::fromString($groupId));
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }
        $gymnast = $this->competitionGroupMemberRepository->find(
            CompetitionGroupMemberId::fromString($competitionGroupMemberId)
        );
        if (!$gymnast) {
            throw new NotFoundHttpException();
        }
        $aboutCompetitionGroupMember = $this->aboutGymnastRepository->findForCompetitionGroupMember(
            CompetitionGroupMemberId::fromString($competitionGroupMemberId)
        );

        return new Response(
            $this->twig->render(
                '@PublicInformation/competitive_gymnastics/about_competition_group_member.html.twig',
                [
                    'group'                       => $competitionGroup,
                    'aboutCompetitionGroupMember' => $aboutCompetitionGroupMember,
                    'gymnast'                     => $gymnast,
                ]
            )
        );
    }
}
