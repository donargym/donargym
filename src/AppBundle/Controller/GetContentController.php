<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class GetContentController extends BaseController
{
    protected $calendarItems;
    protected $header;

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * @Route("/", name="getIndexPage")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        return($this->getNieuwsPageAction('index', $request));
    }

    /**
     * @Route("/donar/{page}/", defaults={"page" = "geschiedenis"}, name="getDonarPage")
     * @Method("GET")
     */
    public function getDonarPageAction($page)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('geschiedenis', 'visie', 'bestuur', 'leiding', 'evenementen', 'locaties', 'kleding', 'vacatures', 'sponsors')))
        {
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
            FROM AppBundle:Content content
            WHERE content.pagina = :page
            ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('donar/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/nieuws/{page}/", defaults={"page" = "index"}, name="getNieuwsPage")
     * @Method("GET")
     */
    public function getNieuwsPageAction($page, Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('index', 'vakanties', 'clubblad', 'archief')))
        {
            switch($page)
            {
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
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    protected function getNieuwsIndexPage($jaar = null)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT nieuwsbericht
            FROM AppBundle:Nieuwsbericht nieuwsbericht
            ORDER BY nieuwsbericht.id       DESC');
        $content = $query->setMaxResults(10)->getResult();
        $nieuwsItems = array();
        for($i=0;$i<count($content);$i++)
        {
            $nieuwsItems[$i] = $content[$i]->getAll();
        }
        return $this->render('default/nieuws.html.twig', array(
            'nieuwsItems' => $nieuwsItems,
            'calendarItems' => $this->calendarItems,
            'inlogRole' => $this->inlogRole,
            'header' => $this->header
        ));
    }

    protected function getNieuwsVakantiesPage()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT vakanties
            FROM AppBundle:Vakanties vakanties
            WHERE vakanties.tot >= :datum
            ORDER BY vakanties.van')
            ->setParameter('datum', date('Y-m-d',time()));
        $content = $query->getResult();
        $vakantieItems = array();
        for($i=0;$i<count($content);$i++)
        {
            $vakantieItems[$i] = $content[$i]->getAll();
        }
        return $this->render('default/vakanties.html.twig', array(
            'vakantieItems' => $vakantieItems,
            'calendarItems' => $this->calendarItems,
            'inlogRole' => $this->inlogRole,
            'header' => $this->header
        ));
    }

    protected function getNieuwsClubbladPage()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT clubblad
            FROM AppBundle:Clubblad clubblad
            WHERE clubblad.datum >= :datum
            ORDER BY clubblad.datum DESC')
            ->setParameter('datum', (date('Y',time())-2).'-01-01');
        $content = $query->getResult();
        $clubbladItems = array();$j=0;$k=0;
        for($i=0;$i<count($content);$i++)
        {
            if(date('Y', time()) - date('Y', strtotime($content[$i]->getDatum())) != $k)
            {
                $j=0;
            }
            $k = (date('Y', time()) - date('Y', strtotime($content[$i]->getDatum())));
            $clubbladItems[$k][$j] = $content[$i]->getAll();
            $clubbladItems[$k][$j]->jaar = date('Y', strtotime($content[$i]->getDatum()));
            $clubbladItems[$k][$j]->maandJaar = $this->maand(date('m', strtotime($content[$i]->getDatum()))).' '.date('Y', strtotime($content[$i]->getDatum()));
            $j++;
        }
        return $this->render('default/clubblad.html.twig', array(
            'clubbladItems' => $clubbladItems,
            'calendarItems' => $this->calendarItems,
            'inlogRole' => $this->inlogRole,
            'header' => $this->header
        ));
    }

    protected function getNieuwsArchiefPage(Request $request)
    {
        if($request->query->get('jaar'))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT nieuwsbericht
            FROM AppBundle:Nieuwsbericht nieuwsbericht
            WHERE nieuwsbericht.jaar = :jaar
            ORDER BY nieuwsbericht.id ASC')
            ->setParameter('jaar', $request->query->get('jaar'));
            $content = $query->getResult();
            $nieuwsItems = array();
            for($i=0;$i<count($content);$i++)
            {
                $nieuwsItems[$i] = $content[$i]->getAll();
            }
            return $this->render('default/nieuws.html.twig', array(
                'nieuwsItems' => $nieuwsItems,
                'calendarItems' => $this->calendarItems,
                'inlogRole' => $this->inlogRole,
                'header' => $this->header
            ));
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT nieuwsbericht
                FROM AppBundle:Nieuwsbericht nieuwsbericht
                ORDER BY nieuwsbericht.jaar ASC');
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            $jaren = array();
            for($i = date('Y', time()); $i >= $content->getJaar(); $i--)
            {
                array_push($jaren, $i);
            }
            return $this->render('default/archief_index.html.twig', array(
                'jaren' => $jaren,
                'calendarItems' => $this->calendarItems,
                'inlogRole' => $this->inlogRole,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/lessen/{page}/", defaults={"page" = "lesrooster"}, name="getLessenPage")
     * @Method("GET")
     */
    public function getLessenPageAction($page)
    {
        $this->header = 'bannerrecreatie'.rand(1,5);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('lesrooster', 'peuterenkleutergym', 'gymnastiekenrecreatiefturnen', '50plusgymenconditie', 'aerobicsenbodyshape', 'badmintonenvolleybal')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('lessen/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/wedstrijdturnen/{page}/{view}/{id}",
     * defaults={"page" = "wedstrijdturnen", "view" = null, "id" = null}, name="getWedstrijdturnenPage")
     * @Method("GET")
     */
    public function getWedstrijdturnenPageAction($page)
    {
        $this->header = 'wedstrijdturnen'.rand(1,11);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('wedstrijdturnen')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('wedstrijdturnen/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        elseif(in_array($page, array('voorselectiedenhaag', 'voorselectieleidschendam', 'aselectiedenhaag', 'aselectieleidschendam', 'bselectiedenhaag')))
        {

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/lidmaatschap/{page}/", defaults={"page" = "lidmaatschap"}, name="getLidmaatschapPage")
     * @Method("GET")
     */
    public function getLidmaatschapPageAction($page)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('lidmaatschap', 'contributie', 'formulieren', 'ooievaarspas')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('lidmaatschap/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/fotofilm/{page}/", defaults={"page" = "fotoenfilm"}, name="getFotofilmPage")
     * @Method("GET")
     */
    public function getFotofilmPageAction($page)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('fotoenfilm', 'foto', 'film')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('fotofilm/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/vrijwilligers/{page}/", defaults={"page" = "vrijwilligers"}, name="getVrijwilligersPage")
     * @Method("GET")
     */
    public function getVrijwilligersPageAction($page)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('vrijwilligers', 'taken', 'vrijwilligersdag')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('vrijwilligers/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/contact/{page}/", defaults={"page" = "contact"}, name="getContactPage")
     * @Method("GET")
     */
    public function getContactPageAction($page)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('contact', 'veelgesteldevragen')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('contact/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/inloggen/{page}/", defaults={"page" = "inloggen"}, name="getInloggenPage")
     * @Method("GET")
     */
    public function getInloggenPageAction($page)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('inloggen')))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT content
                FROM AppBundle:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC')
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($content) > 0)
            {
                return $this->render('inloggen/index.html.twig', array(
                    'content' => $content->getContent(),
                    'calendarItems' => $this->calendarItems,
                    'inlogRole' => $this->inlogRole,
                    'header' => $this->header
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/donar/{page}/", name="setDonarPage")
     * @Method("POST")
     */
    public function updatePageAction($page, Request $request)
    {
        if($this->session->get("inlog") === "admin")
        {
            if(in_array($page,array('informatie', 'locatie', 'sponsors', 'reglementen', 'contact', 'zaterdag', 'zondag', 'fotos')))
            {
                $content = new Content();
                $content->setGewijzigd(new \DateTime("now"));
                $content->setPagina($page);
                $content->setContent($request->request->get('content'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($content);
                $em->flush();
                return $this->render('default/index.html.twig');
            }
            else
            {
                $this->session->getFlashBag()->add('Error', 'De pagina kan niet gevonden worden');
            }
        }
        else
        {
            $this->session->getFlashBag()->add('Error', 'Niet ingelogd als admin');
        }
    }
}
