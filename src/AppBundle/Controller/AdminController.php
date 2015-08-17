<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Content;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends BaseController
{
    protected $header;
    protected $calendarItems;

    public function __construct()
    {
    }

    /**
     * @Route("/admin/", name="getAdminIndexPage")
     * @Method("GET")
     */
    public function getIndexPageAction()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }
    
    /**
     * @Route("/admin/foto/", name="getAdminFotoPage")
     * @Method("GET")
     */
    public function getAdminFotoPage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminFotos.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }

    /**
     * @Route("/admin/bestanden/", name="getAdminBestandenPage")
     * @Method("GET")
     */
    public function getAdminBestandenPage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminUploads.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }

    /**
     * @Route("/admin/selectie/", name="getAdminSelectiePage")
     * @Method("GET")
     */
    public function getAdminSelectiePage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminSelectie.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }
}