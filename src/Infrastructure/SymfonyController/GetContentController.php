<?php

namespace App\Infrastructure\SymfonyController;

use App\Infrastructure\DoctrineDbal\DbalAboutGymnastRepository;
use App\Infrastructure\DoctrineDbal\DbalClubMagazineRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionGroupMemberRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionGroupRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionResultRepository;
use App\Infrastructure\DoctrineDbal\DbalHolidayRepository;
use App\Infrastructure\DoctrineDbal\DbalNewsPostRepository;
use App\Infrastructure\DoctrineDbal\DbalSimpleContentPageRepository;
use App\Infrastructure\SymfonyMailer\SymfonyMailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class GetContentController extends BaseController
{
    private DbalSimpleContentPageRepository $simpleContentPageRepository;
    private DbalNewsPostRepository $newsPostRepository;
    private DbalHolidayRepository $holidayRepository;
    private DbalClubMagazineRepository $clubMagazineRepository;
    private DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository;
    private DbalCompetitionGroupRepository $competitionGroupRepository;
    private DbalCompetitionResultRepository $competitionResultRepository;
    private DbalAboutGymnastRepository $aboutGymnastRepository;
    private SymfonyMailer $mailer;
    private Environment $twig;

    public function __construct(
        DbalSimpleContentPageRepository $simpleContentPageRepository,
        DbalNewsPostRepository $newsPostRepository,
        DbalHolidayRepository $holidayRepository,
        DbalClubMagazineRepository $clubMagazineRepository,
        DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository,
        DbalCompetitionGroupRepository $competitionGroupRepository,
        DbalCompetitionResultRepository $competitionResultRepository,
        DbalAboutGymnastRepository $aboutGymnastRepository,
        SymfonyMailer $mailer,
        Environment $twig
    )
    {
        $this->simpleContentPageRepository      = $simpleContentPageRepository;
        $this->newsPostRepository               = $newsPostRepository;
        $this->holidayRepository                = $holidayRepository;
        $this->clubMagazineRepository           = $clubMagazineRepository;
        $this->competitionGroupMemberRepository = $competitionGroupMemberRepository;
        $this->competitionGroupRepository       = $competitionGroupRepository;
        $this->competitionResultRepository      = $competitionResultRepository;
        $this->aboutGymnastRepository           = $aboutGymnastRepository;
        $this->mailer                           = $mailer;
        $this->twig                             = $twig;
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
     * @Route("/vakanties", name="holidays", methods={"GET"})
     */
    public function holidays(): Response
    {
        return new Response(
            $this->twig->render(
                'default/holidays.html.twig',
                ['holidays' => $this->holidayRepository->findCurrentAndFutureHolidays()]
            )
        );
    }

    /**
     * @Route("/clubblad", name="clubMagazine", methods={"GET"})
     */
    public function clubMagazine(): Response
    {
        return new Response(
            $this->twig->render(
                'default/club_magazine.html.twig',
                [
                    'clubMagazines' => $this->clubMagazineRepository->findAll(),
                    'years'         => $this->clubMagazineRepository->findAllYears(),
                ]
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

    /**
     * @Route("/wedstrijdturnen/{groupId}", name="showCompetitionGroup", methods={"GET"})
     */
    public function showCompetitionGroup(int $groupId): Response
    {
        $competitionGroup = $this->competitionGroupRepository->find($groupId);
        if (!$competitionGroup) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'wedstrijdturnen/competition_group.html.twig',
            [
                'group'                 => $competitionGroup,
                'competionGroupMembers' => $this->competitionGroupMemberRepository->findAllForGroup($groupId),
            ]
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

        return $this->render(
            'wedstrijdturnen/competition_results.html.twig',
            [
                'group'              => $competitionGroup,
                'competitionResults' => $this->competitionResultRepository->findAllForGroup($groupId),
            ]
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

        return $this->render(
            'wedstrijdturnen/about_gymnast.html.twig',
            [
                'group'        => $competitionGroup,
                'aboutGymnast' => $aboutGymnast,
                'gymnast'      => $gymnast,
            ]
        );
    }

    /**
     * @Route("/lidmaatschap/{page}/", defaults={"page" = "lidmaatschap"}, name="getLidmaatschapPage", methods={"GET"})
     */
    public function getLidmaatschapPageAction($page)
    {
        $em           = $this->getDoctrine()->getManager();
        $query        = $em->createQuery(
            'SELECT formulieren
                FROM App:Formulieren formulieren
                ORDER BY formulieren.id'
        );
        $content      = $query->getResult();
        $contentItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'lidmaatschap/formulieren.html.twig',
            array(
                'contentItems' => $contentItems,
            )
        );
    }

    /**
     * @Route("/contact/{page}/", defaults={"page" = "contact"}, name="getContactPage", methods={"GET"})
     */
    public function getContactPageAction($page)
    {
        $em           = $this->getDoctrine()->getManager();
        $query        = $em->createQuery(
            'SELECT veelgesteldevragen
                FROM App:VeelgesteldeVragen veelgesteldevragen
                ORDER BY veelgesteldevragen.id'
        );
        $content      = $query->getResult();
        $contentItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'contact/veelgesteldeVragen.html.twig',
            array(
                'contentItems' => $contentItems,
            )
        );
    }

    /**
     * @Route("/agenda/view/{id}/", name="getAgendaPage", methods={"GET"})
     */
    public function getAgendaPageAction($id)
    {
        $em      = $this->getDoctrine()->getManager();
        $query   = $em->createQuery(
            'SELECT calendar
                FROM App:Calendar calendar
                WHERE calendar.id = :id'
        )
            ->setParameter('id', $id);
        $content = $query->setMaxResults(1)->getOneOrNullResult();
        if ($content) {
            return $this->render(
                'default/viewCalendar.html.twig',
                array(
                    'content' => $content->getAll(),
                )
            );
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }
}
