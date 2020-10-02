<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\Controller;

use App\CompetitionGroup\Infrastructure\DoctrineDbal\DbalCompetitionGroupMemberRepository;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\CompetitionSeason;
use App\Shared\Domain\Security\UserStorage;
use App\Shared\Domain\SystemClock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @IsGranted("ROLE_COMPETITION_GROUP")
 */
final class MemberController
{
    private Environment                          $twig;
    private UserStorage                          $userStorage;
    private RouterInterface                      $router;
    private DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository;
    private SystemClock                          $clock;
    private string                               $floorMusicLocationFromWebRoot;
    private string                               $profilePictureLocationFromWebRoot;

    public function __construct(
        Environment $twig,
        UserStorage $userStorage,
        RouterInterface $router,
        DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository,
        SystemClock $clock
    ) {
        $this->twig                              = $twig;
        $this->userStorage                       = $userStorage;
        $this->router                            = $router;
        $this->competitionGroupMemberRepository  = $competitionGroupMemberRepository;
        $this->clock                             = $clock;
        $this->floorMusicLocationFromWebRoot     = '/uploads/vloermuziek/';
        $this->profilePictureLocationFromWebRoot = '/uploads/selectiefotos/';
    }

    /**
     * @Route("/login/wedstrijdturnen/{competitionGroupMemberId}", name="competitionGroupMember", methods={"GET", "POST"})
     */
    public function competitionGroupMember(string $competitionGroupMemberId): Response
    {
        $user   = $this->userStorage->getUser();
//        $member = $this->competitionGroupMemberRepository->findDetailedCompetitionGroupMember(
//            CompetitionGroupMemberId::fromString($competitionGroupMemberId),
//            $user->userId()
//        );
        $member = $this->competitionGroupMemberRepository->findDetailedCompetitionGroupMember(
            CompetitionGroupMemberId::fromString($competitionGroupMemberId),
            $user->userId()
        );
        if (!$member) {
            throw new NotFoundHttpException();
        }



//        $totalPresence = $this->trainingParticipationRepository->findTotalTrainingPresenceNumbers(
//            $member->id(),
//            CompetitionSeason::getCompetitionSeasonForDate($this->clock->now())
//        );

        return new Response(
            $this->twig->render(
                '@CompetitionGroup/competition_group_login/competition_group_member.html.twig',
                [
                    'member'                            => $member,
                    'floorMusicLocationFromWebRoot'     => $this->floorMusicLocationFromWebRoot,
                    'profilePictureLocationFromWebRoot' => $this->profilePictureLocationFromWebRoot,
//                    'totalPresence'                     => $totalPresence, // deze moet per groep!
                ]
            )
        );
    }
}
