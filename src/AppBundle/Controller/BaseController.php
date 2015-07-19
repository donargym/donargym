<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Calendar;
use AppBundle\Entity\Content;
use AppBundle\Entity\Nieuws;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;


class BaseController extends Controller
{
    protected $session;
    protected $inlogRole;

    public function __construct()
    {
        $this->session = new Session();
        $this->inlogRole = $this->getInlogRole();
    }

    public function getCalendarItems()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT calendar
                FROM AppBundle:Calendar calendar
                WHERE calendar.datum >= :datum
                ORDER BY calendar.datum ASC')
            ->setParameter('datum', date('Y-m-d',time()));
        $calendar = $query->getResult();
        $calendarItems = array();
        for($i=0;$i<count($calendar);$i++)
        {
            $calendarItems[$i] = $calendar[$i]->getAll();
        }
        return $calendarItems;
    }

    public function getInlogRole()
    {
        if($this->session->get('inlogRole'))
        {
            return $this->session->get('inlogRole');
        }
        else
        {
            return "visitor";
        }
    }
}