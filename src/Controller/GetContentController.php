<?php

namespace App\Controller;

use App\Entity\Functie;
use App\Entity\Groepen;
use App\Entity\Persoon;
use App\Entity\Stukje;
use App\Entity\TrainingsstageTrainer;
use App\Entity\TrainingsstageTurnster;
use App\Form\Type\TrainingsstageTrainerType;
use App\Form\Type\TrainingsstageTurnsterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class GetContentController extends BaseController
{
    private $paymentLink = 'https://bunq.me/open-request/t/a8b04702-6632-47ca-9a96-7722edf8d25c';

    public function __construct()
    {
    }

    /**
     * @Route("/trainingsstage/", name="trainingsstage", methods={"GET", "POST"})
     */
    public function trainingsstage(Request $request, MailerInterface $mailer)
    {
        $this->setBasicPageData();
        if (!$request->query->get('as')) {
            return $this->render(
                'wedstrijdturnen/trainingsstageIndexPage.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }

        if ($request->query->get('as') === 'turnster') {
            $trainingsstageTurnster = new TrainingsstageTurnster();
            $form                   = $this->createForm(TrainingsstageTurnsterType::class, $trainingsstageTurnster);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $file */
                $file     = $trainingsstageTurnster->getInsuranceCard();
                $fileName = $trainingsstageTurnster->getName() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('upload_dir'),
                    $fileName
                );
                $trainingsstageTurnster->setInsuranceCard($fileName);

                $this->addToDB($trainingsstageTurnster);

                $this->sendEmail(
                    'Inschrijving trainingsstage',
                    $trainingsstageTurnster->getEmailaddress(),
                    'mails/trainingsstage_confirmation.txt.twig',
                    $mailer,
                    array(
                        'naam'        => $trainingsstageTurnster->getName(),
                        'paymentLink' => $this->paymentLink,
                    )
                );

                return $this->redirectToRoute('trainingsstageSuccess', array('as' => $request->query->get('as')));
            }

            return $this->render(
                'wedstrijdturnen/trainingsstageTurnsterForm.html.twig',
                array(
                    'form'               => $form->createView(),
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                    'paymentLink'        => $this->paymentLink,
                )
            );
        }

        if ($request->query->get('as') === 'trainer') {
            $trainingsstageTrainer = new TrainingsstageTrainer();
            $form                  = $this->createForm(TrainingsstageTrainerType::class, $trainingsstageTrainer);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $file */
                $file     = $trainingsstageTrainer->getInsuranceCard();
                $fileName = $trainingsstageTrainer->getName() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('upload_dir'),
                    $fileName
                );
                $trainingsstageTrainer->setInsuranceCard($fileName);

                $this->addToDB($trainingsstageTrainer);

                $this->sendEmail(
                    'Inschrijving trainingsstage',
                    $trainingsstageTrainer->getEmailaddress(),
                    'mails/trainingsstage_trainer_confirmation.txt.twig',
                    $mailer,
                    array(
                        'naam' => $trainingsstageTrainer->getName(),
                    )
                );

                return $this->redirectToRoute('trainingsstageSuccess', array('as' => $request->query->get('as')));
            }

            return $this->render(
                'wedstrijdturnen/trainingsstageTrainerForm.html.twig',
                array(
                    'form'               => $form->createView(),
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                    'paymentLink'        => $this->paymentLink,
                )
            );
        }

        return $this->redirectToRoute('getIndexPage');
    }

    /**
     * @Route("/trainingsstageSuccess/", name="trainingsstageSuccess", methods={"GET"})
     */
    public function trainingsstageSuccess(Request $request)
    {
        $this->setBasicPageData();
        return $this->render(
            'wedstrijdturnen/trainingsstageSuccessPage.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
                'as'                 => $request->query->get('as'),
                'paymentLink'        => $this->paymentLink,
            )
        );
    }

    /**
     * @Route("/", name="getIndexPage", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        return ($this->getNieuwsPageAction('index', $request));
    }

    /**
     * @Route("/donar/{page}/", defaults={"page" = "geschiedenis"}, name="getDonarPage", methods={"GET"})
     */
    public function getDonarPageAction($page)
    {
        $this->setBasicPageData();
        if (in_array(
            $page,
            array(
                'geschiedenis',
                'visie',
                'bestuur',
                'leiding',
                'evenementen',
                'locaties',
                'kleding',
                'vacatures',
                'sponsors'
            )
        )) {
            $this->calendarItems = $this->getCalendarItems();
            $em                  = $this->getDoctrine()->getManager();
            $query               = $em->createQuery(
                'SELECT content
            FROM App:Content content
            WHERE content.pagina = :page
            ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content             = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                return $this->render(
                    'donar/index.html.twig',
                    array(
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/{page}/", defaults={"page" = "index"}, name="getNieuwsPage", methods={"GET"})
     */
    public function getNieuwsPageAction($page, Request $request)
    {
        $this->setBasicPageData();
        if (in_array($page, array('index', 'vakanties', 'clubblad', 'archief'))) {
            switch ($page) {
                case 'index':
                    return $this->getNieuwsIndexPage();
                    break;
                case 'vakanties':
                    return $this->getNieuwsVakantiesPage();
                    break;
                case 'clubblad':
                    return $this->getNieuwsClubbladPage();
                    break;
                case 'archief':
                    return $this->getNieuwsArchiefPage($request);
                    break;
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    protected function getNieuwsIndexPage($jaar = null)
    {
        $em          = $this->getDoctrine()->getManager();
        $query       = $em->createQuery(
            'SELECT nieuwsbericht
            FROM App:Nieuwsbericht nieuwsbericht
            ORDER BY nieuwsbericht.id       DESC'
        );
        $content     = $query->setMaxResults(10)->getResult();
        $nieuwsItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $nieuwsItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'default/nieuws.html.twig',
            array(
                'nieuwsItems'        => $nieuwsItems,
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    protected function getNieuwsVakantiesPage()
    {
        $em            = $this->getDoctrine()->getManager();
        $query         = $em->createQuery(
            'SELECT vakanties
            FROM App:Vakanties vakanties
            WHERE vakanties.tot >= :datum
            ORDER BY vakanties.van'
        )
            ->setParameter('datum', date('Y-m-d', time()));
        $content       = $query->getResult();
        $vakantieItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $vakantieItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'default/vakanties.html.twig',
            array(
                'vakantieItems'      => $vakantieItems,
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    protected function getNieuwsClubbladPage()
    {
        $em            = $this->getDoctrine()->getManager();
        $query         = $em->createQuery(
            'SELECT clubblad
            FROM App:Clubblad clubblad
            WHERE clubblad.datum >= :datum
            ORDER BY clubblad.datum DESC'
        )
            ->setParameter('datum', (date("Y", time()) - 2) . '-01-01');
        $content       = $query->getResult();
        $clubbladItems = array();
        $j             = 0;
        $k             = 0;
        for ($i = 0; $i < count($content); $i++) {
            if (date("Y", time()) - date("Y", strtotime($content[$i]->getDatumFormat())) != $k) {
                $j = 0;
            }
            $k                                = (date("Y", time()) - date(
                    "Y",
                    strtotime($content[$i]->getDatumFormat())
                ));
            $clubbladItems[$k][$j]            = $content[$i]->getAll();
            $clubbladItems[$k][$j]->jaar      = date("Y", strtotime($content[$i]->getDatumFormat()));
            $clubbladItems[$k][$j]->maandJaar = $this->maand(
                    date("m", strtotime($content[$i]->getDatumFormat()))
                ) . ' ' . date("Y", strtotime($content[$i]->getDatumFormat()));
            $j++;
        }
        return $this->render(
            'default/clubblad.html.twig',
            array(
                'clubbladItems'      => $clubbladItems,
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    protected function getNieuwsArchiefPage(Request $request)
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
                    'nieuwsItems'        => $nieuwsItems,
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
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
                    'jaren'              => $jaren,
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/lessen/{page}/", defaults={"page" = "lesrooster"}, name="getLessenPage", methods={"GET"})
     */
    public function getLessenPageAction($page)
    {
        $this->setBasicPageData('recreatie');
        if (in_array(
            $page,
            array(
                'lesrooster',
                'peuterenkleutergym',
                'gymnastiekenrecreatiefturnen',
                '50plusgymenconditie',
                'aerobicsenbodyshape',
                'badmintonenvolleybal'
            )
        )) {
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
                    'lessen/index.html.twig',
                    array(
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
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
        $this->setBasicPageData('wedstrijdturnen');
        $groepId = $this->groepIds;
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
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'groepen'            => $this->groepItems,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems' => $this->calendarItems,
                        'header'        => $this->header
                    )
                );
            }
        } elseif (in_array($page, $groepId)) {
            $em          = $this->getDoctrine()->getManager();
            $query       = $em->createQuery(
                'SELECT groepen
                FROM App:Groepen groepen
                WHERE groepen.id = :id'
            )
                ->setParameter('id', $page);
            $groep       = $query->setMaxResults(1)->getOneOrNullResult();
            $groepIdName = $groep->getIdName();
            if ($view == null && $id == null) {
                return $this->render(
                    'wedstrijdturnen/groepIndex.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'activeGroep'        => $groepIdName,
                        'wedstrijdLinkItems' => $this->groepItems,
                        'groepen'            => $this->groepItems,
                    )
                );
            } elseif ($view == 'wedstrijduitslagen') {
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
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'activeGroep'        => $groepIdName,
                        'groepen'            => $this->groepItems,
                        'wedstrijduitslagen' => $wedstrijduitslagen,
                        'wedstrijdLinkItems' => $this->groepItems,
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
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'activeGroep'        => $groepIdName,
                        'groepen'            => $this->groepItems,
                        'personen'           => $personen,
                        'wedstrijdLinkItems' => $this->groepItems,
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
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'activeGroep'        => $groepIdName,
                        'groepen'            => $this->groepItems,
                        'stukje'             => $stukjeItems,
                        'wedstrijdLinkItems' => $this->groepItems,
                        'turnster'           => $turnster,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/lidmaatschap/{page}/", defaults={"page" = "lidmaatschap"}, name="getLidmaatschapPage", methods={"GET"})
     */
    public function getLidmaatschapPageAction($page)
    {
        $this->setBasicPageData();
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
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
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
                    'contentItems'       => $contentItems,
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/fotofilm/{page}/", defaults={"page" = "fotoenfilm"}, name="getFotofilmPage", methods={"GET"})
     */
    public function getFotofilmPageAction($page)
    {
        $this->setBasicPageData();
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
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/vrijwilligers/{page}/", defaults={"page" = "vrijwilligers"}, name="getVrijwilligersPage", methods={"GET"})
     */
    public function getVrijwilligersPageAction($page)
    {
        $this->setBasicPageData();
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
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/contact/{page}/", defaults={"page" = "contact"}, name="getContactPage", methods={"GET"})
     */
    public function getContactPageAction($page)
    {
        $this->setBasicPageData();
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
                        'content'            => $content->getContent(),
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
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
                    'contentItems'       => $contentItems,
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/inloggen/", name="getInloggenPage", methods={"GET"})
     *
     * @IsGranted("ROLE_INGELOGD")
     */
    public function getInloggenPageAction()
    {
        $this->setBasicPageData();
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
                    array(
                        'calendarItems' => $this->calendarItems,
                        'header'        => $this->header
                    )
                );
        }
    }

    /**
     * @Route("/inloggen/new_pass/", name="getNewPassPage", methods={"GET", "POST"})
     */
    public function getNewPassPageAction(Request $request, EncoderFactoryInterface $encoderFactory, MailerInterface $mailer)
    {
        $error = "";
        $this->setBasicPageData();

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
                $password = $this->generatePassword();
                $encoder  = $encoderFactory
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em->flush();

                $message = new TemplatedEmail();
                $message->subject('Inloggegevens website Donar')
                    ->from('noreply@donargym.nl')
                    ->to($user->getUsername())
                    ->textTemplate('mails/new_password.txt.twig')
                    ->context(
                        array(
                            'email1'   => $user->getUsername(),
                            'email2'   => $user->getEmail2(),
                            'email3'   => $user->getEmail3(),
                            'password' => $password
                        )
                    );
                $mailer->send($message);

                if ($user->getEmail2()) {

                    $message = new TemplatedEmail();
                    $message->subject('Inloggegevens website Donar')
                        ->from('noreply@donargym.nl')
                        ->to($user->getEmail2())
                        ->textTemplate('mails/new_password.txt.twig')
                        ->context(
                            array(
                                'email1'   => $user->getUsername(),
                                'email2'   => $user->getEmail2(),
                                'email3'   => $user->getEmail3(),
                                'password' => $password
                            )
                        );
                    $mailer->send($message);
                }

                if ($user->getEmail3()) {

                    $message = new TemplatedEmail();
                    $message->subject('Inloggegevens website Donar')
                        ->from('noreply@donargym.nl')
                        ->to($user->getEmail3())
                        ->textTemplate('mails/new_password.txt.twig')
                        ->context(
                            array(
                                'email1'   => $user->getUsername(),
                                'email2'   => $user->getEmail2(),
                                'email3'   => $user->getEmail3(),
                                'password' => $password
                            )
                        );
                    $mailer->send($message);
                }

                $error = 'Een nieuw wachtwoord is gemaild';
            }
        }

        return $this->render(
            'security/newPass.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'error'              => $error,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/agenda/view/{id}/", name="getAgendaPage", methods={"GET"})
     */
    public function getAgendaPageAction($id)
    {
        $this->setBasicPageData();
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
                    'content'            => $content->getAll(),
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

}
