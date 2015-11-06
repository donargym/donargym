<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Functie;
use AppBundle\Entity\Groepen;
use AppBundle\Entity\Persoon;
use AppBundle\Entity\SelectieFoto;
use AppBundle\Entity\Stukje;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class GetContentController extends BaseController
{

    public function __construct()
    {
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
        $this->setBasicPageData();
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
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/nieuws/{page}/", defaults={"page" = "index"}, name="getNieuwsPage")
     * @Method("GET")
     */
    public function getNieuwsPageAction($page, Request $request)
    {
        $this->setBasicPageData();
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
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
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
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
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
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
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
            if(date('Y', time()) - date('Y', strtotime($content[$i]->getDatumFormat())) != $k)
            {
                $j=0;
            }
            $k = (date('Y', time()) - date('Y', strtotime($content[$i]->getDatumFormat())));
            $clubbladItems[$k][$j] = $content[$i]->getAll();
            $clubbladItems[$k][$j]->jaar = date('Y', strtotime($content[$i]->getDatumFormat()));
            $clubbladItems[$k][$j]->maandJaar = $this->maand(date('m', strtotime($content[$i]->getDatumFormat()))).' '.date('Y', strtotime($content[$i]->getDatumFormat()));
            $j++;
        }
        return $this->render('default/clubblad.html.twig', array(
            'clubbladItems' => $clubbladItems,
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
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
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
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
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/lessen/{page}/", defaults={"page" = "lesrooster"}, name="getLessenPage")
     * @Method("GET")
     */
    public function getLessenPageAction($page)
    {
        $this->setBasicPageData('recreatie');
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
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/wedstrijdturnen/{page}/{view}/{id}", defaults={"page" = "wedstrijdturnen", "view" = null, "id" = null}, name="getWedstrijdturnenPage")
     * @Method("GET")
     * @param $page
     * @param $view
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getWedstrijdturnenPageAction($page, $view, $id)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $groepId = $this->groepIds;
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
                    'header' => $this->header,
                    'groepen' => $this->groepItems,
                    'wedstrijdLinkItems' => $this->groepItems,
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
        elseif(in_array($page, $groepId))
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT groepen
                FROM AppBundle:Groepen groepen
                WHERE groepen.id = :id')
                ->setParameter('id', $page);
            $groep = $query->setMaxResults(1)->getOneOrNullResult();
            $groepIdName = $groep->getIdName();
            if ($view == null && $id == null) {
                return $this->render('wedstrijdturnen/groepIndex.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'activeGroep' =>$groepIdName,
                    'wedstrijdLinkItems' => $this->groepItems,
                    'groepen' => $this->groepItems,
                ));
            }
            elseif ($view == 'wedstrijduitslagen') {
                $wedstrijduitslagen = array();
                $uitslagen = $groep->getWedstrijduitslagen();
                for ($counter=(count($uitslagen)-1); $counter>=0;$counter--) {
                    if ($uitslagen[$counter]->getDatum()->format('m')>7) {
                        $wedstrijduitslagen[$uitslagen[$counter]->getDatum()->format('Y')][] = $uitslagen[$counter]->getAll();
                    } else {
                        $wedstrijduitslagen[($uitslagen[$counter]->getDatum()->format('Y')-1)][] = $uitslagen[$counter]->getAll();
                    }
                }
                return $this->render('wedstrijdturnen/wedstrijduitslagen.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'activeGroep' =>$groepIdName,
                    'groepen' => $this->groepItems,
                    'wedstrijduitslagen' => $wedstrijduitslagen,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            elseif ($view == 'TNT' && $id==null) {
                $personen = array();
                $personen['Trainer'] = array();
                $personen['Assistent-Trainer'] = array();
                $personen['Turnster'] = array();
                foreach ($groep->getPeople() as $persoon) {
                    $functies = $persoon->getFunctie();
                    /** @var Functie $functie */
                    foreach ($functies as $functie) {
                        /** @var Groepen $groep */
                        $groep = $functie->getGroep();
                        if($groep->getId() == $page) {
                            $personen[$functie->getFunctie()][] = $persoon->getAll();
                        }
                    }
                }
                usort($personen['Turnster'], function($a, $b)
                {
                    $t1 = strtotime($a->geboortedatum);
                    $t2 = strtotime($b->geboortedatum);
                    return $t1-$t2;
                });
                return $this->render('wedstrijdturnen/tnt.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'activeGroep' =>$groepIdName,
                    'groepen' => $this->groepItems,
                    'personen' => $personen,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            } elseif($view == 'TNT' && $id!=null) {
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                    'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
                    ->setParameter('id', $id);
                /** @var Persoon $persoon */
                $persoon = $query->setMaxResults(1)->getOneOrNullResult();
                /** @var Stukje $stukje */
                $turnster = $persoon->getAll();
                $stukje = $persoon->getStukje();
                $stukjeItems = $stukje->getAll();
                return $this->render('wedstrijdturnen/stukje.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'activeGroep' =>$groepIdName,
                    'groepen' => $this->groepItems,
                    'stukje' => $stukjeItems,
                    'wedstrijdLinkItems' => $this->groepItems,
                    'turnster' => $turnster,
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/lidmaatschap/{page}/", defaults={"page" = "lidmaatschap"}, name="getLidmaatschapPage")
     * @Method("GET")
     */
    public function getLidmaatschapPageAction($page)
    {
        $this->setBasicPageData();
        if(in_array($page, array('lidmaatschap', 'contributie', 'ooievaarspas')))
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
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }

        }
        elseif($page =  'formulieren')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT formulieren
                FROM AppBundle:Formulieren formulieren
                ORDER BY formulieren.id');
            $content = $query->getResult();
            $contentItems = array();
            for($i=0;$i<count($content);$i++)
            {
                $contentItems[$i] = $content[$i]->getAll();
            }
            return $this->render('lidmaatschap/formulieren.html.twig', array(
                'contentItems' => $contentItems,
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/fotofilm/{page}/", defaults={"page" = "fotoenfilm"}, name="getFotofilmPage")
     * @Method("GET")
     */
    public function getFotofilmPageAction($page)
    {
        $this->setBasicPageData();
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
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/vrijwilligers/{page}/", defaults={"page" = "vrijwilligers"}, name="getVrijwilligersPage")
     * @Method("GET")
     */
    public function getVrijwilligersPageAction($page)
    {
        $this->setBasicPageData();
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
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }

        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/contact/{page}/", defaults={"page" = "contact"}, name="getContactPage")
     * @Method("GET")
     */
    public function getContactPageAction($page)
    {
        $this->setBasicPageData();
        if(in_array($page, array('contact')))
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
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }

        }
        elseif($pagina = 'veelgesteldevragen')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT veelgesteldevragen
                FROM AppBundle:VeelgesteldeVragen veelgesteldevragen
                ORDER BY veelgesteldevragen.id');
            $content = $query->getResult();
            $contentItems = array();
            for($i=0;$i<count($content);$i++)
            {
                $contentItems[$i] = $content[$i]->getAll();
            }
            return $this->render('contact/veelgesteldeVragen.html.twig', array(
                'contentItems' => $contentItems,
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/inloggen/", name="getInloggenPage")
     *
     * @Security("has_role('ROLE_INGELOGD')")
     *
     * @Method("GET")
     */
    public function getInloggenPageAction()
    {
        $this->setBasicPageData();
        $user = $this->getUser();
        $roles = $user->getRoles();
        switch ($roles[0])
        {
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
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
                ));
        }
    }

    /**
     * @Route("/inloggen/new_pass/", name="getNewPassPage")
     * @Method({"GET", "POST"})
     */
    public function getNewPassPageAction(Request $request)
    {
        $error = "";
        $this->setBasicPageData();

        if($request->getMethod() == 'POST')
        {
            $email = $this->get('request')->request->get('email');
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT user
                    FROM AppBundle:User user
                    WHERE user.username = :email
                    OR user.email2 = :email')
                    ->setParameter('email', $email);
            $user = $query->setMaxResults(1)->getOneOrNullResult();
            if (count($user) == 0) {
                $error = 'Dit Emailadres komt niet voor in de database';
            }
            else {
                $password = $this->generatePassword();
                $encoder = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em->flush();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Inloggegevens website Donar')
                    ->setFrom('webmaster@donargym.nl')
                    ->setTo($user->getUsername())
                    ->setBody(
                        $this->renderView(
                            'mails/new_password.txt.twig',
                            array(
                                'email1' => $user->getUsername(),
                                'email2' =>$user->getEmail2(),
                                'password' => $password
                            )
                        ),
                        'text/plain'
                    );
                try{$this->get('mailer')->send($message);}
                catch(\Exception $e){
                    var_dump($e->getMessage());die;
                }

                if($user->getEmail2())
                {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Inloggegevens website Donar')
                        ->setFrom('webmaster@donargym.nl')
                        ->setTo($user->getEmail2())
                        ->setBody(
                            $this->renderView(
                                'mails/new_password.txt.twig',
                                array(
                                    'email1' => $user->getUsername(),
                                    'email2' =>$user->getEmail2(),
                                    'password' => $password
                                )
                            ),
                            'text/plain'
                        );
                    try{$this->get('mailer')->send($message);}
                    catch(\Exception $e){
                        var_dump($e->getMessage());die;
                    }
                }
                $error = 'Een nieuw wachtwoord is gemaild';
            }
        }

        return $this->render('security/newPass.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'error' => $error,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Route("/agenda/view/{id}/", name="getAgendaPage")
     * @Method("GET")
     */
    public function getAgendaPageAction($id)
    {
        $this->setBasicPageData();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT calendar
                FROM AppBundle:Calendar calendar
                WHERE calendar.id = :id')
            ->setParameter('id', $id);
        $content = $query->setMaxResults(1)->getOneOrNullResult();
        if(count($content) > 0)
        {
            return $this->render('default/viewCalendar.html.twig', array(
                'content' => $content->getAll(),
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

}
