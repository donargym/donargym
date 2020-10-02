<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\Controller;

use App\CompetitionGroup\Infrastructure\DoctrineDbal\DbalCompetitionGroupAccountRepository;
use App\Shared\Domain\Security\UserStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @IsGranted("ROLE_COMPETITION_GROUP")
 */
final class AccountController
{
    private Environment                                $twig;
    private UserStorage                                $userStorage;
    private DbalCompetitionGroupAccountRepository      $competitionGroupAccountRepository;
    private RouterInterface                            $router;

    public function __construct(
        Environment $twig,
        UserStorage $userStorage,
        DbalCompetitionGroupAccountRepository $competitionGroupAccountRepository,
        RouterInterface $router
    ) {
        $this->twig                              = $twig;
        $this->userStorage                       = $userStorage;
        $this->competitionGroupAccountRepository = $competitionGroupAccountRepository;
        $this->router                            = $router;
    }

    /**
     * @Route("/login/wedstrijdturnen", name="competitionGroupLoginIndex", methods={"GET", "POST"})
     */
    public function competitionGroupLoginIndex(): Response
    {
        $user                    = $this->userStorage->getUser();
        $competitionGroupAccount = $this->competitionGroupAccountRepository->findForUser($user->userId());

        return new Response(
            $this->twig->render(
                '@CompetitionGroup/competition_group_login/account_information.html.twig',
                [
                    'competitionGroupAccount' => $competitionGroupAccount,
                ]
            )
        );
    }
}
