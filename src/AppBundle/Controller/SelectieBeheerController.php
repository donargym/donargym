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
 * @Security("has_role('ROLE_SELECTIE')")
 */
class SelectieBeheerController extends BaseController
{
    protected $header;
    protected $calendarItems;

    public function __construct()
    {
    }

    /**
     * @Route("/selectiebeheer/", name="getSelectieBeheerIndexPage")
     * @Method("GET")
     */
    public function getIndexPageAction()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/SelectieBeheerIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }
}