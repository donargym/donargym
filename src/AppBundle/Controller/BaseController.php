<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Calendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Content;
use AppBundle\Entity\Nieuws;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;


class BaseController extends Controller
{

    public function __construct()
    {
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

    public function maand($maandNummer)
    {
        switch($maandNummer)
        {
            case '01': return 'Januari'; break;
            case '02': return 'Februari'; break;
            case '03': return 'Maart'; break;
            case '04': return 'April'; break;
            case '05': return 'Mei'; break;
            case '06': return 'Juni'; break;
            case '07': return 'Juli'; break;
            case '08': return 'Augustus'; break;
            case '09': return 'September'; break;
            case '10': return 'Oktober'; break;
            case '11': return 'November'; break;
            case '12': return 'December'; break;
        }
    }

    public function generatePassword($length = 8)
    {
        $password = "";
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        $maxlength = strlen($possible);
        if ($length > $maxlength)
        {
            $length = $maxlength;
        }
        $i = 0;
        while ($i < $length)
        {
            $char = substr($possible, mt_rand(0, $maxlength-1), 1);
            if (!strstr($password, $char))
            {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }
}