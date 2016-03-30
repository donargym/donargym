<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Scores;
use AppBundle\Entity\ToegestaneNiveaus;
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
    const DATUM_HBC = '17 april 2016';
    const LOCATIE_HBC = 'Sporthal Overbosch';

    protected $calendarItems;
    protected $header;
    protected $groepItems;
    protected $groepIds;

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
        if (count ($groepen) > 0) {
            $groepItems = array();
            $groepId = array();
            for($i=0;$i<count($groepen);$i++)
            {
                $groepItems[$i] = $groepen[$i]->getIdName();
                $groepId[] = $groepen[$i]->getId();
            }
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

    protected function checkIfNiveauToegestaan($categorie, $niveau)
    {
        /** @var ToegestaneNiveaus $result */
        $result = $this->getDoctrine()->getRepository("AppBundle:ToegestaneNiveaus")
            ->findOneBy(array(
                'categorie' => $categorie,
                'niveau' => $niveau,
            ));
        if (!$result) {
            return false;
        }
        if (($this->getUser() && $this->getUser()->getRole() == 'ROLE_ORGANISATIE') ||
            $result->getUitslagGepubliceerd()) {
            return true;
        }
        return false;
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

    protected function setBasicPageData($page = null)
    {
        $wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $wedstrijdLinkItems[0];
        $this->groepIds = $wedstrijdLinkItems[1];
        $this->header = $this->getHeader($page);
        $this->calendarItems = $this->getCalendarItems();
    }

    protected function getRanking($scores, $order = '')
    {
        $toestellen = array('Sprong', 'Brug', 'Balk', 'Vloer', '');
        foreach ($toestellen as $toestel) {
            usort($scores, function ($a, $b) use ($toestel) {
                if ($a['totaal' . $toestel] == $b['totaal' . $toestel]) {
                    return 0;
                }
                return ($a['totaal' . $toestel] > $b['totaal' . $toestel]) ? -1 : 1;
            });
            for ($i = 1; $i <= count($scores); $i++) {
                if ($i == 1) {
                    $scores[($i - 1)]['rank' . $toestel] = $i;
                } elseif ($scores[($i - 1)]['totaal' . $toestel] == $scores[($i - 2)]['totaal' . $toestel]) {
                    $scores[($i - 1)]['rank' . $toestel] = $scores[($i - 2)]['rank' . $toestel];
                } else {
                    $scores[($i - 1)]['rank' . $toestel] = $i;
                }
            }
        }
        usort($scores, function ($a, $b) use ($order) {
            if ($a['totaal' . $order] == $b['totaal' . $order]) {
                return 0;
            }
            return ($a['totaal' . $order] > $b['totaal' . $order]) ? -1 : 1;
        });
        return $scores;
    }

    protected function getToegestaneNiveaus()
    {
        $toegestaneNiveaus = array();
        $repo = $this->getDoctrine()->getRepository('AppBundle:ToegestaneNiveaus');
        /** @var ToegestaneNiveaus[] $results */
        if ($this->getUser() && $this->getUser()->getRole() == 'ROLE_ORGANISATIE') {
            $results = $repo->findAll();
        } else {
            $results = $repo->findBy(array(
                'uitslagGepubliceerd' => 1,
            ));
        }
        foreach ($results as $result) {
            /** @var ToegestaneNiveaus[] $results */
            $toegestaneNiveaus[$result->getCategorie()][$result->getId()] = array(
                'niveau' => $result->getNiveau(),
                'uitslagGepubliceerd' => $result->getUitslagGepubliceerd(),
            );
        }
        return $toegestaneNiveaus;
    }

    /**
     * @Route("/updateScores/{wedstrijdnummer}/", name="updateScores")
     * @Method({"GET"})
     */
    public function updateScores(Request $request, $wedstrijdnummer)
    {
        if ($request->query->get('key') && $request->query->get('key') === $this->getParameter('update_scores_string')) {
            $toestellen = array('sprong', 'brug', 'balk', 'vloer');
            if ($request->query->get('toestel') && in_array(strtolower($request->query->get('toestel')), $toestellen)) {
                /** @var Scores $score */
                $score = $this->getDoctrine()->getRepository('AppBundle:Scores')
                    ->findOneBy(array('wedstrijdnummer' => $wedstrijdnummer));
                if ($score) {
                    switch (strtolower($request->query->get('toestel'))) {
                        case 'sprong':
                            if ($request->query->get('dSprong1') !== null && $request->query->get('eSprong1') !== null &&
                                $request->query->get('nSprong1') !== null && $request->query->get('dSprong2') !== null &&
                                $request->query->get('eSprong2') !== null && $request->query->get('nSprong2') !== null) {
                                try {
                                    $score->setDSprong1($request->query->get('dSprong1'));
                                    $score->setESprong1($request->query->get('eSprong1'));
                                    $score->setNSprong1($request->query->get('nSprong1'));
                                    $score->setDSprong2($request->query->get('dSprong2'));
                                    $score->setESprong2($request->query->get('eSprong2'));
                                    $score->setNSprong2($request->query->get('nSprong2'));
                                    $score->setUpdatedSprong(new \DateTime('now'));
                                    $this->addToDB($score);
                                } catch (\Exception $e) {
                                    return new Response($e->getMessage(), $e->getCode());
                                }
                                header('location: ' . $this->getParameter('returnLocation'));
                                return new Response('ok', 200);
                            } else {
                                return new Response('Niet alle verplichte gegevens zijn opgegeven', 500);
                            }
                            break;
                        case 'brug':
                            if ($request->query->get('dBrug') !== null && $request->query->get('eBrug') !== null &&
                                $request->query->get('nBrug') !== null) {
                                try {
                                    $score->setDBrug($request->query->get('dBrug'));
                                    $score->setEBrug($request->query->get('eBrug'));
                                    $score->setNBrug($request->query->get('nBrug'));
                                    $score->setUpdatedBrug(new \DateTime('now'));
                                    $this->addToDB($score);
                                } catch (\Exception $e) {
                                    return new Response($e->getMessage(), $e->getCode());
                                }
                                header('location: ' . $this->getParameter('returnLocation'));
                                return new Response('ok', 200);
                            } else {
                                return new Response('Niet alle verplichte gegevens zijn opgegeven', 500);
                            }
                            break;
                        case 'balk':
                            if ($request->query->get('dBalk') !== null && $request->query->get('eBalk') !== null &&
                                $request->query->get('nBalk') !== null) {
                                try {
                                    $score->setDBalk($request->query->get('dBalk'));
                                    $score->setEBalk($request->query->get('eBalk'));
                                    $score->setNBalk($request->query->get('nBalk'));
                                    $score->setUpdatedBalk(new \DateTime('now'));
                                    $this->addToDB($score);
                                } catch (\Exception $e) {
                                    return new Response($e->getMessage(), $e->getCode());
                                }
                                header('location: ' . $this->getParameter('returnLocation'));
                                return new Response('ok', 200);
                            } else {
                                return new Response('Niet alle verplichte gegevens zijn opgegeven', 500);
                            }
                            break;
                        case 'vloer':
                            if ($request->query->get('dVloer') !== null && $request->query->get('eVloer') !== null &&
                                $request->query->get('nVloer') !== null) {
                                try {
                                    $score->setDVloer($request->query->get('dVloer'));
                                    $score->setEVloer($request->query->get('eVloer'));
                                    $score->setNVloer($request->query->get('nVloer'));
                                    $score->setUpdatedVloer(new \DateTime('now'));
                                    $this->addToDB($score);
                                } catch (\Exception $e) {
                                    return new Response($e->getMessage(), $e->getCode());
                                }
                                header('location: ' . $this->getParameter('returnLocation'));
                                return new Response('ok', 200);
                            } else {
                                return new Response('Niet alle verplichte gegevens zijn opgegeven', 500);
                            }
                            break;
                    }
                } else {
                    return new Response('Geen geldig wedstrijdnummer', 500);
                }
            } else {
                return new Response('Invalid toestel', 500);
            }
            return new Response('Iternal server error', 500);
        }
        return new Response('Invalid key!', 403);
    }

    /**
     * @Route("/publiceerUitslag/{categorie}/{niveau}/", name="publiceerUitslag")
     * @Method({"GET"})
     */
    public function publiceerUitslag(Request $request, $categorie, $niveau)
    {
        if ($request->query->get('key') && $request->query->get('key') === $this->getParameter('update_scores_string')) {
            /** @var ToegestaneNiveaus $result */
            $result = $this->getDoctrine()->getRepository('AppBundle:ToegestaneNiveaus')
                ->findOneBy(array(
                    'categorie' => $categorie,
                    'niveau' => $niveau,
                ));
            if ($result) {
                try {
                    $result->setUitslagGepubliceerd(true);
                    $this->addToDB($result);
                    header('location: ' . $this->getParameter('returnLocation'));
                    return new Response('ok', 200);
                } catch (\Exception $e) {
                    return new Response($e->getMessage(), $e->getCode());
                }
            } else {
                return new Response('Combinatie van niveau/categorie niet gevonden!', 500);
            }
        }
        return new Response('Invalid key!', 403);
    }

    protected function addToDB($object, $detach = null)
    {
        $em = $this->getDoctrine()->getManager();
        if ($detach) {
            $em->detach($detach);
        }
        $em->persist($object);
        $em->flush();
    }

    protected function removeFromDB($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();
    }
}