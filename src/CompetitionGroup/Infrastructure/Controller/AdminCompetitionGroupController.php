<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\Controller;

use App\CompetitionGroup\Infrastructure\DoctrineDbal\DbalCompetitionGroupWithTrainersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @IsGranted("ROLE_ADMIN")
 */
final class AdminCompetitionGroupController
{
    private DbalCompetitionGroupWithTrainersRepository $competitionGroupWithTrainersRepository;
    private Environment                                $twig;

    public function __construct(
        DbalCompetitionGroupWithTrainersRepository $competitionGroupWithTrainersRepository,
        Environment $twig
    ) {
        $this->competitionGroupWithTrainersRepository = $competitionGroupWithTrainersRepository;
        $this->twig                                   = $twig;
    }

    /**
     * @Route("/admin/selectie/", name="adminCompetitionGroups", methods={"GET"})
     */
    public function adminCompetitionGroups()
    {
        $competitionGroups = $this->competitionGroupWithTrainersRepository->findAll();

        return new Response(
            $this->twig->render(
                '@CompetitionGroup/admin/competition_groups.html.twig',
                ['competitionGroups' => $competitionGroups]
            )
        );
    }
}
