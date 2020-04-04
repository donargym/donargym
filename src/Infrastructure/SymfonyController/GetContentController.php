<?php

namespace App\Infrastructure\SymfonyController;

use App\Domain\EmailAddress;
use App\Domain\EmailTemplateType;
use App\Domain\PasswordGenerator;
use App\Entity\Persoon;
use App\Entity\Stukje;
use App\Infrastructure\DoctrineDbal\DbalClubMagazineRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionGroupMemberRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionGroupRepository;
use App\Infrastructure\DoctrineDbal\DbalCompetitionResultRepository;
use App\Infrastructure\DoctrineDbal\DbalHolidayRepository;
use App\Infrastructure\DoctrineDbal\DbalNewsPostRepository;
use App\Infrastructure\DoctrineDbal\DbalSimpleContentPageRepository;
use App\Infrastructure\SymfonyMailer\SymfonyMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Twig\Environment;

class GetContentController extends BaseController
{
    private DbalSimpleContentPageRepository $simpleContentPageRepository;
    private DbalNewsPostRepository $newsPostRepository;
    private DbalHolidayRepository $holidayRepository;
    private DbalClubMagazineRepository $clubMagazineRepository;
    private DbalCompetitionGroupMemberRepository $competitionGroupMemberRepository;
    private DbalCompetitionGroupRepository $competitionGroupRepository;
    private DbalCompetitionResultRepository $competitionResultRepository;
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
        $competitionGroupMembers = $this->competitionGroupMemberRepository->findAllForGroup($groupId);

        return $this->render(
            'wedstrijdturnen/competition_group.html.twig',
            [
                'group'                 => $competitionGroup,
                'competionGroupMembers' => $competitionGroupMembers,
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

        $competitionResults = $this->competitionResultRepository->findAllForGroup($groupId);

        return $this->render(
            'wedstrijdturnen/competition_results.html.twig',
            [
                'group'              => $competitionGroup,
                'competitionResults' => $competitionResults,
            ]
        );
    }

    /**
     * @Route("/wedstrijdturnen/{page}/{view}/{id}", defaults={"page" = "wedstrijdturnen", "view" = null, "id" = null}, name="getWedstrijdturnenPage", methods={"GET"})
     */
    public function getWedstrijdturnenPageAction($page, $view, $id)
    {
        $em    = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT groepen
                FROM App:Groepen groepen
                WHERE groepen.id = :id'
        )
            ->setParameter('id', $page);
        $groep = $query->setMaxResults(1)->getOneOrNullResult();
        if ($groep) {
            $groepIdName = $groep->getIdName();
        }

        if ($view == 'TNT' && $id != null) {
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT persoon
                FROM App:Persoon persoon
                WHERE persoon.id = :id'
            )
                ->setParameter('id', $id);
            /** @var Persoon $persoon */
            $persoon = $query->setMaxResults(1)->getOneOrNullResult();
            /** @var Stukje $stukje */
            $turnster = $persoon->getAll();
            $stukje   = $persoon->getStukje();
            if (!$stukje) {
                $stukje = new Stukje();
                $persoon->setStukje($stukje);
                $em = $this->getDoctrine()->getManager();
                $em->persist($stukje);
                $em->flush();
            }
            $stukjeItems = $stukje->getAll();
            return $this->render(
                'wedstrijdturnen/stukje.html.twig',
                array(
                    'activeGroep' => $groepIdName,
                    'stukje'      => $stukjeItems,
                    'turnster'    => $turnster,
                )
            );
        }
        return $this->render(
            'error/pageNotFound.html.twig',
            array()
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
     * @Route("/inloggen/", name="getInloggenPage", methods={"GET"})
     *
     * @IsGranted("ROLE_INGELOGD")
     */
    public function getInloggenPageAction()
    {
        $user  = $this->getUser();
        $roles = $user->getRoles();
        switch ($roles[0]) {
            case 'ROLE_ADMIN':
                return $this->redirectToRoute('getAdminIndexPage');
                break;
            case 'ROLE_TRAINER':
            case 'ROLE_ASSISTENT':
            case 'ROLE_TURNSTER':
                return $this->redirectToRoute('getSelectieIndexPage');
                break;
            default:
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
        }
    }

    /**
     * @Route("/inloggen/new_pass/", name="getNewPassPage", methods={"GET", "POST"})
     */
    public function getNewPassPageAction(Request $request, EncoderFactoryInterface $encoderFactory)
    {
        $error = "";

        if ($request->getMethod() == 'POST') {
            $email = $request->request->get('email');
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT user
                    FROM App:User user
                    WHERE user.username = :email
                    OR user.email2 = :email
                    OR user.email3 = :email
                    '
            )
                ->setParameter('email', $email);
            $user  = $query->setMaxResults(1)->getOneOrNullResult();
            if (!$user) {
                $error = 'Dit Emailadres komt niet voor in de database';
            } else {
                $password = PasswordGenerator::generatePassword();
                $encoder  = $encoderFactory
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em->flush();

                $subject          = 'Inloggegevens website Donar';
                $templateLocation = 'mails/new_password.txt.twig';
                $parameters       = [
                    'email1'   => $user->getUsername(),
                    'email2'   => $user->getEmail2(),
                    'email3'   => $user->getEmail3(),
                    'password' => $password,
                ];

                $this->mailer->sendEmail(
                    $subject,
                    EmailAddress::fromString($user->getUsername()),
                    $templateLocation,
                    EmailTemplateType::TEXT(),
                    $parameters
                );

                if ($user->getEmail2()) {
                    $this->mailer->sendEmail(
                        $subject,
                        EmailAddress::fromString($user->getEmail2()),
                        $templateLocation,
                        EmailTemplateType::TEXT(),
                        $parameters
                    );
                }

                if ($user->getEmail3()) {
                    $this->mailer->sendEmail(
                        $subject,
                        EmailAddress::fromString($user->getEmail3()),
                        $templateLocation,
                        EmailTemplateType::TEXT(),
                        $parameters
                    );
                }

                $error = 'Een nieuw wachtwoord is gemaild';
            }
        }

        return $this->render(
            'security/newPass.html.twig',
            array(
                'error' => $error,
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

    /**
     * @Route("/page/{pageName}/", name="simpleContentPage", methods={"GET"})
     */
    public function simpleContentPage(string $pageName): Response
    {
        $simpleContentPage = $this->simpleContentPageRepository->getMostRecentContentForPage($pageName);
        if (!$simpleContentPage) {
            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render(
                'default/simple_content_page.html.twig',
                ['content' => $simpleContentPage->pageContent()]
            )
        );
    }
}
