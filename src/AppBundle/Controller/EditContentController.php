<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Calendar;
use AppBundle\Entity\Clubblad;
use AppBundle\Entity\Formulieren;
use AppBundle\Entity\Nieuwsbericht;
use AppBundle\Entity\Vakanties;
use AppBundle\Entity\VeelgesteldeVragen;
use AppBundle\Form\Type\CalendarType;
use AppBundle\Form\Type\ContentType;
use AppBundle\Form\Type\NieuwsberichtType;
use AppBundle\Form\Type\VakantiesType;
use AppBundle\Form\Type\VeelgesteldeVragenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Content;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class EditContentController extends BaseController
{
    protected $header;
    protected $calendarItems;

    public function __construct()
    {
    }

    /**
     * @Route("/donar/{page}/edit/", defaults={"page" = "geschiedenis"}, name="editDonarPage")
     * @Method({"GET", "POST"})
     */
    public function editDonarPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getDonarPage', array('page' => $page));
                }
                else {
                    return $this->render('donar/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView()
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
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/lessen/{page}/edit/", defaults={"page" = "lesrooster"}, name="editLessenPage")
     * @Method({"GET", "POST"})
     */
    public function editLessenPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getLessenPage', array('page' => $page));
                }
                else {
                    return $this->render('lessen/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView()
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
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/wedstrijdturnen/{page}/edit/",
     * defaults={"page" = "wedstrijdturnen"}, name="editWedstrijdturnenPage")
     * @Method({"GET", "POST"})
     */
    public function editWedstrijdturnenPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getWedstrijdturnenPage', array('page' => $page));
                }
                else {
                    return $this->render('wedstrijdturnen/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView()
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
     * @Route("/lidmaatschap/{page}/edit/", defaults={"page" = "lidmaatschap"}, name="editLidmaatschapPage")
     * @Method({"GET", "POST"})
     */
    public function editLidmaatschapPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getLidmaatschapPage', array('page' => $page));
                }
                else {
                    return $this->render('lidmaatschap/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView()
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
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/fotofilm/{page}/edit/", defaults={"page" = "fotoenfilm"}, name="editFotofilmPage")
     * @Method({"GET", "POST"})
     */
    public function editFotofilmPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getFotofilmPage', array('page' => $page));
                }
                else {
                    return $this->render('fotofilm/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView()
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
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/vrijwilligers/{page}/edit/", defaults={"page" = "vrijwilligers"}, name="editVrijwilligersPage")
     * @Method({"GET", "POST"})
     */
    public function editVrijwilligersPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getVrijwilligersPage', array('page' => $page));
                }
                else {
                    return $this->render('vrijwilligers/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView(),
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
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/contact/{page}/edit/", defaults={"page" = "contact"}, name="editContactPage")
     * @Method({"GET", "POST"})
     */
    public function editContactPageAction($page, Request $request)
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
                $form = $this->createForm(new ContentType(), $content);
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getContactPage', array('page' => $page));
                }
                else {
                    return $this->render('contact/editIndex.html.twig', array(
                        'content' => $content->getContent(),
                        'calendarItems' => $this->calendarItems,
                        'header' => $this->header,
                        'form' => $form->createView()
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
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/agenda/add/", name="addAgendaPage")
     * @Method({"GET", "POST"})
     */
    public function addAgendaPage(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $agenda = new Calendar();
        $form = $this->createForm(new CalendarType(), $agenda);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($agenda);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
        }
        else {
            return $this->render('default/addCalendar.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/agenda/edit/{id}/", name="editAgendaPage")
     * @Method({"GET", "POST"})
     */
    public function editAgendaPage($id, Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT calendar
                FROM AppBundle:Calendar calendar
                WHERE calendar.id = :id')
            ->setParameter('id', $id);
        $agenda = $query->setMaxResults(1)->getOneOrNullResult();
        if(count($agenda) > 0)
        {
            $form = $this->createForm(new CalendarType(), $agenda);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($agenda);
                $em->flush();
                return $this->redirectToRoute('getNieuwsPage');
            }
            else {
                return $this->render('default/addCalendar.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'form' => $form->createView()
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
     * @Route("/agenda/remove/{id}/", name="removeAgendaPage")
     * @Method({"GET", "POST"})
     */
    public function removeAgendaPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT calendar
                FROM AppBundle:Calendar calendar
                WHERE calendar.id = :id')
                ->setParameter('id', $id);
            $agenda = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($agenda) > 0)
            {
                return $this->render('default/removeCalendar.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $agenda->getAll()
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
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT calendar
                FROM AppBundle:Calendar calendar
                WHERE calendar.id = :id')
                ->setParameter('id', $id);
            $agenda = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($agenda);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
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
     * @Route("/nieuws/index/add/", name="addNieuwsPage")
     * @Method({"GET", "POST"})
     */
    public function addNieuwsPage(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $nieuwsbericht = new Nieuwsbericht();
        $form = $this->createForm(new NieuwsberichtType(), $nieuwsbericht);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $nieuwsbericht->setDatumtijd(date('d-m-Y: H:i', time()));
            $nieuwsbericht->setJaar(date('Y', time()));
            $nieuwsbericht->setBericht(str_replace("\n","<br />",$nieuwsbericht->getBericht()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($nieuwsbericht);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
        }
        else {
            return $this->render('default/addNieuwsbericht.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/nieuws/index/edit/{id}/", name="editNieuwsberichtPage")
     * @Method({"GET", "POST"})
     */
    public function editNieuwsberichtPage($id, Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT nieuwsbericht
                FROM AppBundle:Nieuwsbericht nieuwsbericht
                WHERE nieuwsbericht.id = :id')
            ->setParameter('id', $id);
        $nieuwsbericht = $query->setMaxResults(1)->getOneOrNullResult();
        $nieuwsbericht->setBericht(str_replace("<br />","\n",$nieuwsbericht->getBericht()));
        if(count($nieuwsbericht) > 0)
        {
            $form = $this->createForm(new NieuwsberichtType(), $nieuwsbericht);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $nieuwsbericht->setBericht(str_replace("\n","<br />",$nieuwsbericht->getBericht()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($nieuwsbericht);
                $em->flush();
                return $this->redirectToRoute('getNieuwsPage');
            }
            else {
                return $this->render('default/addNieuwsbericht.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'form' => $form->createView()
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
     * @Route("/nieuws/index/remove/{id}/", name="removeNieuwsberichtPage")
     * @Method({"GET", "POST"})
     */
    public function removeNieuwsberichtPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT nieuwsbericht
                FROM AppBundle:Nieuwsbericht nieuwsbericht
                WHERE nieuwsbericht.id = :id')
                ->setParameter('id', $id);
            $nieuwsbericht = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($nieuwsbericht) > 0)
            {
                return $this->render('default/removeNieuwsbericht.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $nieuwsbericht->getAll()
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
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT nieuwsbericht
                FROM AppBundle:Nieuwsbericht nieuwsbericht
                WHERE nieuwsbericht.id = :id')
                ->setParameter('id', $id);
            $nieuwsbericht = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($nieuwsbericht);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
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
     * @Route("/nieuws/vakanties/add/", name="addVakantiesPage")
     * @Method({"GET", "POST"})
     */
    public function addVakantiesPage(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
        $vakanties = new Vakanties();
        $form = $this->createForm(new VakantiesType(), $vakanties);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vakanties);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'vakanties'));
        }
        else {
            return $this->render('default/addVakanties.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'vakantieItems' => $vakantieItems
            ));
        }
    }

    /**
     * @Route("/nieuws/vakanties/edit/{id}/", name="editVakantiesPage")
     * @Method({"GET", "POST"})
     */
    public function editVakantiesPage($id, Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
        $query = $em->createQuery(
            'SELECT vakanties
                FROM AppBundle:Vakanties vakanties
                WHERE vakanties.id = :id')
            ->setParameter('id', $id);
        $vakanties = $query->setMaxResults(1)->getOneOrNullResult();
        if(count($vakanties) > 0)
        {
            $form = $this->createForm(new VakantiesType(), $vakanties);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($vakanties);
                $em->flush();
                return $this->redirectToRoute('getNieuwsPage', array('page' => 'vakanties'));
            }
            else {
                return $this->render('default/addVakanties.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'form' => $form->createView(),
                    'vakantieItems' => $vakantieItems
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
     * @Route("/nieuws/vakanties/remove/{id}/", name="removeVakantiesPage")
     * @Method({"GET", "POST"})
     */
    public function removeVakantiesPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
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
            $query = $em->createQuery(
                'SELECT vakanties
                FROM AppBundle:Vakanties vakanties
                WHERE vakanties.id = :id')
                ->setParameter('id', $id);
            $vakanties = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($vakanties) > 0)
            {
                return $this->render('default/removeVakanties.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $vakanties->getAll(),
                    'vakantieItems' => $vakantieItems
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
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT vakanties
                FROM AppBundle:Vakanties vakanties
                WHERE vakanties.id = :id')
                ->setParameter('id', $id);
            $vakanties = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($vakanties);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'vakanties'));
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
     * @Template()
     * @Route("/nieuws/clubblad/add/", name="addClubbladPage")
     * @Method({"GET", "POST"})
     */
    public function addClubbladPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $clubblad = new Clubblad();
        $form = $this->createFormBuilder($clubblad)
            ->add('datum', 'date', array(
                'widget' => 'single_text',
            ))
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clubblad);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'clubblad'));
        }
        else {
            return $this->render('default/addClubblad.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/nieuws/clubblad/remove/{id}/", name="removeClubbladPage")
     * @Method({"GET", "POST"})
     */
    public function removeClubbladPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT clubblad
                FROM AppBundle:Clubblad clubblad
                WHERE clubblad.id = :id')
                ->setParameter('id', $id);
            $clubblad = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($clubblad) > 0)
            {
                return $this->render('default/removeClubblad.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $clubblad->getAll(),
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
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT clubblad
                FROM AppBundle:Clubblad clubblad
                WHERE clubblad.id = :id')
                ->setParameter('id', $id);
            $clubblad = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($clubblad);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'clubblad'));
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
     * @Template()
     * @Route("/lidmaatschap/formulieren/add/", name="addFormulierenPage")
     * @Method({"GET", "POST"})
     */
    public function addFormulierenPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $formulier = new Formulieren();
        $form = $this->createFormBuilder($formulier)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($formulier);
            $em->flush();
            return $this->redirectToRoute('getLidmaatschapPage', array('page' => 'formulieren'));
        }
        else {
            return $this->render('lidmaatschap/addFormulieren.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/lidmaatschap/formulieren/remove/{id}/", name="removeFormulierenPage")
     * @Method({"GET", "POST"})
     */
    public function removeFormulierenPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT formulieren
                FROM AppBundle:Formulieren formulieren
                WHERE formulieren.id = :id')
                ->setParameter('id', $id);
            $formulier = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($formulier) > 0)
            {
                return $this->render('lidmaatschap/removeFormulieren.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $formulier->getAll(),
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
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT formulieren
                FROM AppBundle:Formulieren formulieren
                WHERE formulieren.id = :id')
                ->setParameter('id', $id);
            $formulier = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($formulier);
            $em->flush();
            return $this->redirectToRoute('getLidmaatschapPage', array('page' => 'formulieren'));
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
     * @Route("/contact/veelgesteldevragen/add/", name="addVeelgesteldeVragenPage")
     * @Method({"GET", "POST"})
     */
    public function addVeelgesteldeVragenPage(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $vraag = new VeelgesteldeVragen();
        $form = $this->createForm(new VeelgesteldeVragenType(), $vraag);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vraag);
            $em->flush();
            return $this->redirectToRoute('getContactPage', array('page' => 'veelgesteldevragen'));
        }
        else {
            return $this->render('contact/addVeelgesteldeVragen.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/edit/{id}/", name="editVeelgesteldeVragenPage")
     * @Method({"GET", "POST"})
     */
    public function editVeelgesteldeVragenPage($id, Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT veelgesteldevragen
                FROM AppBundle:VeelgesteldeVragen veelgesteldevragen
                WHERE veelgesteldevragen.id = :id')
            ->setParameter('id', $id);
        $vraag = $query->setMaxResults(1)->getOneOrNullResult();
        if(count($vraag) > 0)
        {
            $form = $this->createForm(new VeelgesteldeVragenType(), $vraag);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($vraag);
                $em->flush();
                return $this->redirectToRoute('getContactPage', array('page' => 'veelgesteldevragen'));
            }
            else {
                return $this->render('contact/addVeelgesteldeVragen.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'form' => $form->createView()
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
     * @Route("/contact/veelgesteldevragen/remove/{id}/", name="removeVeelgesteldeVragenPage")
     * @Method({"GET", "POST"})
     */
    public function removeVeelgesteldeVragenPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT veelgesteldevragen
                FROM AppBundle:VeelgesteldeVragen veelgesteldevragen
                WHERE veelgesteldevragen.id = :id')
                ->setParameter('id', $id);
            $vraag = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($vraag) > 0)
            {
                return $this->render('contact/removeVeelgesteldeVragen.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $vraag->getAll()
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
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT veelgesteldevragen
                FROM AppBundle:VeelgesteldeVragen veelgesteldevragen
                WHERE veelgesteldevragen.id = :id')
                ->setParameter('id', $id);
            $vraag = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($vraag);
            $em->flush();
            return $this->redirectToRoute('getContactPage', array('page' => 'veelgesteldevragen'));
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header
            ));
        }
    }
}