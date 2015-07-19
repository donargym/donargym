<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetContentController extends BaseController
{
    protected $calendarItems;

    public function __construct()
    {
        Parent::__construct();
    }

    /**
     * @Route("/", name="getIndexPage")
     * @Method("GET")
     */
    public function indexAction()
    {
        return($this->getNieuwsPageAction('index'));
    }

    /**
     * @Route("/donar/{page}", defaults={"page" = "geschiedenis"}, name="getDonarPage")
     * @Method("GET")
     */
    public function getDonarPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/nieuws/{page}", defaults={"page" = "index"}, name="getNieuwsPage")
     * @Method("GET")
     */
    public function getNieuwsPageAction($page)
    {
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
                    return $this->getNieuwsArchiefPage();
                    break;
            }
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    protected function getNieuwsIndexPage()
    {
        $content = 'hoi';
        return $this->render('donar/index.html.twig', array(
            'content' => $content,
            'calendarItems' => $this->calendarItems,
            'inlogRole' => $this->inlogRole
        ));
    }

    protected function getNieuwsVakantiesPage()
    {

    }

    protected function getNieuwsClubbladPage()
    {

    }

    protected function getNieuwsArchiefPage()
    {

    }

    /**
     * @Route("/lessen/{page}", defaults={"page" = "lesrooster"}, name="getLessenPage")
     * @Method("GET")
     */
    public function getLessenPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/wedstrijdturnen/{page}", defaults={"page" = "wedstrijdturnen"}, name="getWedstrijdturnenPage")
     * @Method("GET")
     */
    public function getWedstrijdturnenPageAction($page)
    {
        $this->calendarItems = $this->getCalendarItems();
        if(in_array($page, array('wedstrijdturnen', 'voorselectiedenhaag', 'voorselectieleidschendam', 'aselectiedenhaag', 'aselectieleidschendam', 'bselectiedenhaag')))
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/lidmaatschap/{page}", defaults={"page" = "lidmaatschap"}, name="getLidmaatschapPage")
     * @Method("GET")
     */
    public function getLidmaatschapPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/fotofilm/{page}", defaults={"page" = "fotoenfilm"}, name="getFotofilmPage")
     * @Method("GET")
     */
    public function getFotofilmPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/vrijwilligers/{page}", defaults={"page" = "vrijwilligers"}, name="getVrijwilligersPage")
     * @Method("GET")
     */
    public function getVrijwilligersPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/contact/{page}", defaults={"page" = "contact"}, name="getContactPage")
     * @Method("GET")
     */
    public function getContactPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/inloggen/{page}", defaults={"page" = "inloggen"}, name="getInloggenPage")
     * @Method("GET")
     */
    public function getInloggenPageAction($page)
    {
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
                    'inlogRole' => $this->inlogRole
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems
            ));
        }
    }

    /**
     * @Route("/donar/{page}", name="setDonarPage")
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
