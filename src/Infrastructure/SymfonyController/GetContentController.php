<?php

namespace App\Infrastructure\SymfonyController;

use App\Domain\EmailAddress;
use App\Domain\EmailTemplateType;
use App\Domain\PasswordGenerator;
use App\Entity\Functie;
use App\Entity\Groepen;
use App\Entity\Persoon;
use App\Entity\Stukje;
use App\Infrastructure\DoctrineDbal\DbalClubMagazineRepository;
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
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class GetContentController extends BaseController
{
    private DbalSimpleContentPageRepository $simpleContentPageRepository;
    private DbalNewsPostRepository $newsPostRepository;
    private DbalHolidayRepository $holidayRepository;
    private DbalClubMagazineRepository $clubMagazineRepository;
    private TranslatorInterface $translator;
    private SymfonyMailer $mailer;
    private Environment $twig;

    public function __construct(
        DbalSimpleContentPageRepository $simpleContentPageRepository,
        DbalNewsPostRepository $newsPostRepository,
        DbalHolidayRepository $holidayRepository,
        DbalClubMagazineRepository $clubMagazineRepository,
        TranslatorInterface $translator,
        SymfonyMailer $mailer,
        Environment $twig
    )
    {
        $this->simpleContentPageRepository = $simpleContentPageRepository;
        $this->newsPostRepository          = $newsPostRepository;
        $this->holidayRepository           = $holidayRepository;
        $this->clubMagazineRepository      = $clubMagazineRepository;
        $this->translator                  = $translator;
        $this->mailer                      = $mailer;
        $this->twig                        = $twig;
    }

    /**
     * @Route("/", name="newsPosts", methods={"GET"})
     *
     * @return Response
     */
    public function newsPosts(): Response
    {
        return $this->render(
            'default/news.html.twig',
            ['newPosts' => $this->newsPostRepository->findTenMostRecentNewsPosts()]
        );
    }

    /**
     * @Route("/vakanties", name="holidays", methods={"GET"})
     *
     * @return Response
     */
    public function holidays()
    {
        return $this->render(
            'default/holidays.html.twig',
            ['holidays' => $this->holidayRepository->findCurrentAndFutureHolidays()]
        );
    }

    /**
     * @Route("/clubblad", name="clubMagazine", methods={"GET"})
     *
     * @return Response
     */
    public function clubMagazine()
    {
        $clubMagazines = $this->clubMagazineRepository->findAll();
        $years         = $this->clubMagazineRepository->findAllYears();

        return $this->render('default/club_magazine.html.twig', ['clubMagazines' => $clubMagazines, 'years' => $years]);
    }

    /**
     * @Route("/nieuws/{page}/", defaults={"page" = "index"}, name="getNieuwsPage", methods={"GET"})
     * @param string  $page
     * @param Request $request
     *
     * @return Response
     */
    public function getNewsPage(string $page, Request $request): Response
    {
        switch ($page) {
            case 'archief':
                return $this->getNieuwsArchiefPage($request);
                break;
            default:
                throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/page/{pageName}/", name="getSimpleContentPage", methods={"GET"})
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

    /**
     * @Route("/wedstrijdturnen/{page}/{view}/{id}", defaults={"page" = "wedstrijdturnen", "view" = null, "id" = null}, name="getWedstrijdturnenPage", methods={"GET"})
     * @param $page
     * @param $view
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWedstrijdturnenPageAction($page, $view, $id)
    {
        if (in_array($page, array('wedstrijdturnen'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                return $this->render(
                    'wedstrijdturnen/index.html.twig',
                    array(
                        'content' => $content->getContent(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
            }
        }

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
            if ($view == null && $id == null) {
                return $this->render(
                    'wedstrijdturnen/groepIndex.html.twig',
                    array(
                        'activeGroep' => $groepIdName,
                    )
                );
            }
        }

        if ($view == 'wedstrijduitslagen') {
            $wedstrijduitslagen = array();
            $uitslagen          = $groep->getWedstrijduitslagen();
            for ($counter = (count($uitslagen) - 1); $counter >= 0; $counter--) {
                if ($uitslagen[$counter]->getDatum()->format("m") > 7) {
                    $wedstrijduitslagen[$uitslagen[$counter]->getDatum()->format("Y")][]
                        = $uitslagen[$counter]->getAll();
                } else {
                    $wedstrijduitslagen[($uitslagen[$counter]->getDatum()->format("Y") - 1)][]
                        = $uitslagen[$counter]->getAll();
                }
            }
            return $this->render(
                'wedstrijdturnen/wedstrijduitslagen.html.twig',
                array(
                    'activeGroep'        => $groepIdName,
                    'wedstrijduitslagen' => $wedstrijduitslagen,
                )
            );
        } elseif ($view == 'TNT' && $id == null) {
            $personen                      = array();
            $personen['Trainer']           = array();
            $personen['Assistent-Trainer'] = array();
            $personen['Turnster']          = array();
            foreach ($groep->getPeople() as $persoon) {
                $functies = $persoon->getFunctie();
                /** @var Functie $functie */
                foreach ($functies as $functie) {
                    /** @var Groepen $groep */
                    $groep = $functie->getGroep();
                    if ($groep->getId() == $page) {
                        $personen[$functie->getFunctie()][] = $persoon->getAll();
                    }
                }
            }
            usort(
                $personen['Turnster'],
                function ($a, $b) {
                    $t1 = strtotime($a->geboortedatum);
                    $t2 = strtotime($b->geboortedatum);
                    return $t1 - $t2;
                }
            );
            return $this->render(
                'wedstrijdturnen/tnt.html.twig',
                array(
                    'activeGroep' => $groepIdName,
                    'personen'    => $personen,
                )
            );
        } elseif ($view == 'TNT' && $id != null) {
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
    public
    function getLidmaatschapPageAction($page)
    {
        if (in_array($page, array('lidmaatschap', 'contributie', 'ooievaarspas'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                return $this->render(
                    'lidmaatschap/index.html.twig',
                    array(
                        'content' => $content->getContent(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
            }

        } elseif ($page === 'formulieren') {
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
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/fotofilm/{page}/", defaults={"page" = "fotoenfilm"}, name="getFotofilmPage", methods={"GET"})
     */
    public
    function getFotofilmPageAction($page)
    {
        if (in_array($page, array('fotoenfilm', 'foto', 'film'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                return $this->render(
                    'fotofilm/index.html.twig',
                    array(
                        'content' => $content->getContent(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/vrijwilligers/{page}/", defaults={"page" = "vrijwilligers"}, name="getVrijwilligersPage", methods={"GET"})
     */
    public
    function getVrijwilligersPageAction($page)
    {
        if (in_array($page, array('vrijwilligers', 'taken', 'vrijwilligersdag'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                return $this->render(
                    'vrijwilligers/index.html.twig',
                    array(
                        'content' => $content->getContent(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/contact/{page}/", defaults={"page" = "contact"}, name="getContactPage", methods={"GET"})
     */
    public
    function getContactPageAction($page)
    {
        if (in_array($page, array('contact'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                return $this->render(
                    'contact/index.html.twig',
                    array(
                        'content' => $content->getContent(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
            }

        } elseif ($pagina = 'veelgesteldevragen') {
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
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/inloggen/", name="getInloggenPage", methods={"GET"})
     *
     * @IsGranted("ROLE_INGELOGD")
     */
    public
    function getInloggenPageAction()
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
            case 'ROLE_SELECTIE':
                return $this->redirectToRoute('getSelectieBeheerIndexPage');
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
    public
    function getNewPassPageAction(Request $request, EncoderFactoryInterface $encoderFactory)
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
    public
    function getAgendaPageAction($id)
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

    private function getNieuwsArchiefPage(Request $request)
    {
        if ($request->query->get('jaar')) {
            $em          = $this->getDoctrine()->getManager();
            $query       = $em->createQuery(
                'SELECT nieuwsbericht
            FROM App:Nieuwsbericht nieuwsbericht
            WHERE nieuwsbericht.jaar = :jaar
            ORDER BY nieuwsbericht.id ASC'
            )
                ->setParameter('jaar', $request->query->get('jaar'));
            $content     = $query->getResult();
            $nieuwsItems = array();
            for ($i = 0; $i < count($content); $i++) {
                $nieuwsItems[$i] = $content[$i]->getAll();
            }
            return $this->render(
                'default/nieuws.html.twig',
                array(
                    'nieuwsItems' => $nieuwsItems,
                )
            );
        } else {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT nieuwsbericht
                FROM App:Nieuwsbericht nieuwsbericht
                ORDER BY nieuwsbericht.jaar ASC'
            );
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            $jaren   = array();
            for ($i = date("Y", time()); $i >= $content->getJaar(); $i--) {
                array_push($jaren, $i);
            }
            return $this->render(
                'default/archief_index.html.twig',
                array(
                    'jaren' => $jaren,
                )
            );
        }
    }

}
