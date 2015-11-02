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
use AppBundle\Entity\Nieuwsbericht;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;
use AppBundle\Entity\SubDoelen;


class BaseController extends Controller
{

    public $calendarItems;
    public $header;
    public $wedstrijdLinkItems;
    public $groepItems;

    public function __construct()
    {
    }

    public function getHeader($page = null)
    {
        switch($page) {
            case 'wedstrijdturnen': return 'wedstrijdturnen'.rand(1,12); break;
            case 'recreatie': return 'bannerrecreatie'.rand(1,4); break;
            default: return 'bannerhome'.rand(1,4); break;
        }
    }

    public function getwedstrijdLinkItems()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT groepen
                FROM AppBundle:Groepen groepen
                ORDER BY groepen.id ASC');
        $groepen = $query->getResult();
        $groepItems = array();
        for($i=0;$i<count($groepen);$i++)
        {
            $groepItems[$i] = $groepen[$i]->getIdName();
            $groepId[] = $groepen[$i]->getId();
        }
        return array($groepItems, $groepId);
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

    protected function addSubDoelenAanPersoon($persoon)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT doelen
        FROM AppBundle:Doelen doelen
        WHERE doelen.trede IS NULL');
        $doelen = $query->getResult();
        foreach ($doelen as $doel) {
            $subdoelEntity = new SubDoelen();
            $subdoelEntity->setDoel($doel);
            $subdoelEntity->setPersoon($persoon);
            $em->persist($subdoelEntity);
            $em->flush();
        }
    }
}