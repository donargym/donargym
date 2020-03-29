<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Controller\FpdfController as FPDF;
use AppBundle\Entity\Trainingsdata;
use AppBundle\Entity\Trainingsplan;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("has_role('ROLE_ASSISTENT')")
 */
class TrainingsPlanController extends SelectieController
{
    /**
     * @Route("/inloggen/selectie/{persoonId}/Trainingsplan/{groepId}/index/", name="TrainingsplanIndex")
     * @Method({"GET"})
     */
    public function TrainingsplanIndex($persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $trainingsdata = $this->getTrainingsdataVoorKruisjeslijst($userObject, $persoonId, $groepId);
        $trainingsplannen = array();
        foreach ($trainingsdata->trainingen as $trainingsdag) {
            foreach ($trainingsdag->trainingsdata as $trainingsdatum) {
                if ($trainingsplan = $this->getTrainingsplanById($trainingsdatum->id)) {
                    $trainingsplannen[$trainingsdatum->id] = true;
                }
            }
        }
        return $this->render('inloggen/TrainingsplanIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'trainingsdata' => $trainingsdata,
            'trainingsplannen' => $trainingsplannen,
        ));
    }

    /**
     * @Route("/inloggen/selectie/{persoonId}/Trainingsplan/{groepId}/makeGroepjes/{trainingsdatumId}/", name="TrainingsplanmakeGroepjes")
     * @Method({"GET"})
     */
    public function TrainingsplanmakeGroepjes($persoonId, $groepId, $trainingsdatumId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $trainingsdataObject = $this->getTrainingsdatumDetails($userObject, $persoonId, $groepId, $trainingsdatumId);
        $personenAanwezigheid = $this->getPersonenVoorTrainingsdatum($trainingsdataObject, $groepId);
        $trainingsdata = new \stdClass();
        $trainingsdata->id = $trainingsdataObject->getId();
        $lesdatum = $trainingsdataObject->getLesdatum();
        $trainingsdata->lesdatum = $lesdatum->format("d-m-Y");
        $trainingsdata->dag = $this->dayToDutch($lesdatum->getTimestamp());
        return $this->render('inloggen/TrainingsplanMakeGroepjes.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'trainingsdata' => $trainingsdata,
            'personenAanwezigheid' => $personenAanwezigheid,
            'trainingsdatumId' => $trainingsdatumId,
        ));
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getTrainingsplanById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT trainingsplan
            FROM AppBundle:Trainingsplan trainingsplan
            WHERE trainingsplan.trainingsdatumId = :id')
            ->setParameter('id', $id);
        $trainingsplan = $query->setMaxResults(1)->getOneOrNullResult();
        if (count($trainingsplan) == 0)
        {
            return false;
        }
        return ($trainingsplan);
    }
    private function getNaamById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
            FROM AppBundle:Persoon persoon
            WHERE persoon.id = :id')
            ->setParameter('id', $id);
        $persoon = $query->setMaxResults(1)->getOneOrNullResult();
        return ($persoon->getVoornaam() . ' ' . $persoon->getAchternaam());
    }

    /**
     * @Route("/inloggen/selectie/{persoonId}/Trainingsplan/{groepId}/makeTrainingsPlan/{trainingId}/", name="makeTrainingsplan")
     * @Method({"POST", "GET"})
     */
    public function makeTrainingsplan($persoonId, $groepId, $trainingId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $trainingsdataObject = $this->getTrainingsdatumDetails($userObject, $persoonId, $groepId, $trainingId);
        $training = $trainingsdataObject->getTrainingen();
        $tijden = $this->getTijdschemaVoorTrainingen($training);
        $tijdschema = $tijden[0];
        $toestelTijden = $tijden[1];
        $trainingsdata = new \stdClass();
        $trainingsdata->id = $trainingsdataObject->getId();
        $lesdatum = $trainingsdataObject->getLesdatum();
        $trainingsdata->lesdatum = $lesdatum->format("d-m-Y");
        $trainingsdata->dag = $this->dayToDutch($lesdatum->getTimestamp());
        $toestelVolgorde['Sprong'] = array('Sprong', 'Brug', 'Balk', 'Vloer');
        $toestelVolgorde['Brug'] = array('Brug', 'Balk', 'Vloer', 'Sprong');
        $toestelVolgorde['Balk'] = array('Balk', 'Vloer', 'Sprong', 'Brug');
        $toestelVolgorde['Vloer'] = array('Vloer', 'Sprong', 'Brug', 'Balk');
        if ($request->getMethod() == "POST") {
            $seizoen = $this->getSeizoen();
            $toestellen = array('Sprong', 'Brug', 'Balk', 'Vloer');
            foreach ($_POST as $key => $value) {
                if (empty($value)) continue;
                if (!isset ($chosenHoofdDoelen[$value]['begintoestel'])) {
                    $toestelKey = rand(0,(count($toestellen)-1));
                    $chosenHoofdDoelen[$value]['begintoestel'] = $toestellen[$toestelKey];
                    unset ($toestellen[$toestelKey]);
                    $toestellen = array_values($toestellen);
                }
                $chosenHoofdDoelen[$value]['turnsters'][$key]['naam'] = $this->getNaamById($key);
                $chosenHoofdDoelen[$value]['turnsters'][$key]['trainingsDoelen'] = $this->getTrainingsDoelPerTurnster($key, $seizoen);
            }
            if (empty($chosenHoofdDoelen))
            {
                $chosenHoofdDoelen = array();
            }
            ksort($chosenHoofdDoelen);
            if ($trainingsplanObject = $this->getTrainingsplanById($trainingId)) {
                $em = $this->getDoctrine()->getManager();
                $trainingsplanObject->setTrainingsplan(json_encode($chosenHoofdDoelen));
                $em->persist($trainingsplanObject);
                $em->flush();
            } else {
                $em = $this->getDoctrine()->getManager();
                $trainingsplanObject = new Trainingsplan();
                $trainingsplanObject->setTrainingsdatumId($trainingId);
                $trainingsplanObject->setTrainingsplan(json_encode($chosenHoofdDoelen));
                $em->persist($trainingsplanObject);
                $em->flush();
            }
        } elseif ($request->getMethod() == "GET") {
            /** @var Trainingsplan $trainingsplanObject */
            $trainingsplanObject = $this->getTrainingsplanById($trainingId);
            $chosenHoofdDoelen = json_decode($trainingsplanObject->getTrainingsplan(), true);
        }
        $tableWidth = (100/4)*(count($chosenHoofdDoelen));
        return $this->render('inloggen/ViewTrainingsplan.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'trainingsplan' => $chosenHoofdDoelen,
            'trainingsdata' => $trainingsdata,
            'toestelVolgorde' => $toestelVolgorde,
            'tijdschema' => $tijdschema,
            'toestelTijden' => $toestelTijden,
            'tableWidth' => $tableWidth,
        ));
    }

    private function getTijdschemaVoorTrainingen($trainingObject)
    {
        $tijd['van'] = $trainingObject->getTijdvan();
        $tijd['tot'] = $trainingObject->getTijdTot();
        $timestampEind = strtotime($tijd['tot']);
        $timestamp = strtotime($tijd['van']);
        $tijd['startBasis1'] = date('H.i', $timestamp);
        $timestamp += 3*60;
        $tijd['startBasis2'] = date('H.i', $timestamp);
        $timestamp += 3*60;
        $tijd['startBasis3'] = date('H.i', $timestamp);
        $timestamp += 4*60;
        $tijd['startKracht1'] = date('H.i', $timestamp);
        $timestamp += 5*60;
        $tijd['startKracht2'] = date('H.i', $timestamp);
        $timestamp += 5*60;
        $tijd['startKracht3'] = date('H.i', $timestamp);
        $timestamp += 5*60;
        $tijd['startLenigheid1'] = date('H.i', $timestamp);
        $timestamp += 5*60;
        $tijd['startLenigheid2'] = date('H.i', $timestamp);
        $timestamp += 5*60;
        $tijd['startLenigheid3'] = date('H.i', $timestamp);
        $timestamp += 5*60;
        $secondsPerToestel = ($timestampEind - $timestamp)/4;
        $toestelTijden[0] = date('H.i', $timestamp);
        $timestamp += $secondsPerToestel;
        $toestelTijden[1] = date('H.i', $timestamp);
        $timestamp += $secondsPerToestel;
        $toestelTijden[2] = date('H.i', $timestamp);
        $timestamp += $secondsPerToestel;
        $toestelTijden[3] = date('H.i', $timestamp);
        $toestelTijden[4] = date('H.i', $timestampEind);
        return array($tijd, $toestelTijden);
    }

    private function getTrainingsDoelPerTurnster($turnsterId, $seizoen)
    {
        $doelenObject = $this->getDoelenVoorSeizoen($turnsterId, $seizoen);
        $result1 = $this->getDoelDetails($doelenObject);
        $doelen = $result1[0];
        $doelenIdArray = array();
        foreach ($doelen as $doelenData) {
            foreach ($doelenData as $doelId => $doelNaam) {
                $doelenIdArray[] = $doelId;
            }
        }
        $subdoelIds = array();
        $collectedDoelen = array();
        $cijfers = array();
        foreach ($doelenIdArray as $doelId) {
            $collectedDoelen[] = $doelId;
            $array = $this->getDoelOpbouw($doelId, $subdoelIds);
            $doelOpbouw = $array[0];
            $subdoelIds = $array[1];
            $result = $this->getSubdoelIds($subdoelIds, $collectedDoelen);
            $subdoelIds = $result[0];
            $extraDoelen = $result[1];
            $reveseExtraDoelen = array_reverse($extraDoelen);
            $repeat = true;
            while ($repeat) {
                $repeat = false;
                foreach ($reveseExtraDoelen as $id => $extraDoel) {
                    if ($result = $this->getPercentages($extraDoel, $cijfers, $turnsterId)) {
                        $cijfers = $result;
                        unset ($reveseExtraDoelen[$id]);
                        continue;
                    }
                    $repeat = true;
                }
            }
            $cijfers = $this->getPercentages($doelOpbouw, $cijfers, $turnsterId);
        }
        $chosenHoofdDoelen = array();
        for ($i = 0; $i < 3; $i ++) {
            foreach ($doelen as $toestel => $doelenPerToestel) {
                $doelKeuze = array();
                foreach ($doelenPerToestel as $doelId => $doelNaam) {
                    $doelKeuze[$doelId] = round(101 - $cijfers[$doelId . '_hoofd']);
                }
                $random = (rand(1,array_sum($doelKeuze)));
                $som = 0;
                foreach ($doelKeuze as $doelId => $doelCijfer) {
                    $som += $doelCijfer;
                    if ($random <= $som) {
                        $chosenHoofdDoelen[$toestel][$i]['id'] = $doelId;
                        $chosenHoofdDoelen[$toestel][$i]['naam'] = $doelenPerToestel[$doelId];
                        $chosenHoofdDoelen[$toestel][$i]['toestel'] = $toestel;
                        break;
                    }
                }
            }
        }
        $chosenSubdoelIds = array();
        foreach ($chosenHoofdDoelen as $toestel => $hoofdDoelArray) {
            foreach ($hoofdDoelArray as $doelNummer => $hoofddoel) {
                if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                $doelOpbouw = $this->getDoelOpbouw($hoofddoel['id'], array());
                if (count($doelOpbouw[0]->subdoelen) == 0) {
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                    continue;
                }
                for ($i = 1; $i <= count($doelOpbouw[0]->subdoelen); $i++) {
                    if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                    $subdoelSom = 0;
                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $trededoel) {
                        if (isset($trededoel->subdoelId)) {
                            $subdoelSom += $cijfers[$trededoel->id . '_hoofd'];
                        }
                        else {
                            $subdoelSom += $cijfers[$trededoel->id];
                        }
                    }
                    $subdoelPercentage = round($subdoelSom / count($doelOpbouw[0]->subdoelen[$i]->trededoelen));
                    if ($subdoelPercentage >= 80) continue;

                    $doelKeuze = array();
                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $key => $trededoel) {
                        if (in_array($trededoel->id, $chosenSubdoelIds)) continue;
                        $doelKeuze[$key]['id'] = $trededoel->id;
                        $doelKeuze[$key]['naam'] = $trededoel->naam;
                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                        if (isset ($trededoel->subdoelId)) {
                            $doelKeuze[$key]['subdoelId'] = $trededoel->subdoelId;
                        }
                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                        if (isset($trededoel->subdoelId)) {
                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id . '_hoofd']);
                        }
                        else {
                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id]);
                        }
                    }
                    $randMax = 0;
                    foreach ($doelKeuze as $keuze) {
                        $randMax += $keuze['kans'];
                    }
                    $random = (rand(1,$randMax));
                    $som = 0;
                    foreach ($doelKeuze as $key => $value) {
                        $som += $value['kans'];
                        if ($random <= $som) {
                            if (!isset ($value['subdoelId'])) {
                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $value['id'];
                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $value['naam'];
                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $value['toestel'];
                                $chosenSubdoelIds[] = $value['id'];
                                break;
                            }
                            else {
                                $doelOpbouw = $this->getDoelOpbouw($value['id'], array());
                                if (count($doelOpbouw[0]->subdoelen) == 0) {
                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                                    break;
                                }
                                for ($i = 1; $i <= count($doelOpbouw[0]->subdoelen); $i++) {
                                    if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                                    $subdoelSom = 0;
                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $trededoel) {
                                        if (isset($trededoel->subdoelId)) {
                                            $subdoelSom += $cijfers[$trededoel->id . '_hoofd'];
                                        } else {
                                            $subdoelSom += $cijfers[$trededoel->id];
                                        }
                                    }
                                    $subdoelPercentage = round($subdoelSom / count($doelOpbouw[0]->subdoelen[$i]->trededoelen));
                                    if ($subdoelPercentage >= 80) continue;

                                    $doelKeuze = array();
                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $key => $trededoel) {
                                        if (in_array($trededoel->id, $chosenSubdoelIds)) continue;
                                        $doelKeuze[$key]['id'] = $trededoel->id;
                                        $doelKeuze[$key]['naam'] = $trededoel->naam;
                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                        if (isset ($trededoel->subdoelId)) {
                                            $doelKeuze[$key]['subdoelId'] = $trededoel->subdoelId;
                                        }
                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                        if (isset($trededoel->subdoelId)) {
                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id . '_hoofd']);
                                        } else {
                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id]);
                                        }
                                    }
                                    $randMax = 0;
                                    foreach ($doelKeuze as $keuze) {
                                        $randMax += $keuze['kans'];
                                    }
                                    $random = (rand(1, $randMax));
                                    $som = 0;
                                    foreach ($doelKeuze as $key => $value) {
                                        $som += $value['kans'];
                                        if ($random <= $som) {
                                            if (!isset ($value['subdoelId'])) {
                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $value['id'];
                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $value['naam'];
                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $value['toestel'];
                                                $chosenSubdoelIds[] = $value['id'];
                                                break;
                                            }
                                            else {
                                                $doelOpbouw = $this->getDoelOpbouw($value['id'], array());
                                                if (count($doelOpbouw[0]->subdoelen) == 0) {
                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                                                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                                                    break;
                                                }
                                                for ($i = 1; $i <= count($doelOpbouw[0]->subdoelen); $i++) {
                                                    if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                                                    $subdoelSom = 0;
                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $trededoel) {
                                                        if (isset($trededoel->subdoelId)) {
                                                            $subdoelSom += $cijfers[$trededoel->id . '_hoofd'];
                                                        } else {
                                                            $subdoelSom += $cijfers[$trededoel->id];
                                                        }
                                                    }
                                                    $subdoelPercentage = round($subdoelSom / count($doelOpbouw[0]->subdoelen[$i]->trededoelen));
                                                    if ($subdoelPercentage >= 80) continue;

                                                    $doelKeuze = array();
                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $key => $trededoel) {
                                                        if (in_array($trededoel->id, $chosenSubdoelIds)) continue;
                                                        $doelKeuze[$key]['id'] = $trededoel->id;
                                                        $doelKeuze[$key]['naam'] = $trededoel->naam;
                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                        if (isset ($trededoel->subdoelId)) {
                                                            $doelKeuze[$key]['subdoelId'] = $trededoel->subdoelId;
                                                        }
                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                        if (isset($trededoel->subdoelId)) {
                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id . '_hoofd']);
                                                        } else {
                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id]);
                                                        }
                                                    }
                                                    $randMax = 0;
                                                    foreach ($doelKeuze as $keuze) {
                                                        $randMax += $keuze['kans'];
                                                    }
                                                    $random = (rand(1, $randMax));
                                                    $som = 0;
                                                    foreach ($doelKeuze as $key => $value) {
                                                        $som += $value['kans'];
                                                        if ($random <= $som) {
                                                            if (!isset ($value['subdoelId'])) {
                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $value['id'];
                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $value['naam'];
                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $value['toestel'];
                                                                $chosenSubdoelIds[] = $value['id'];
                                                                break;
                                                            }
                                                            else {
                                                                $doelOpbouw = $this->getDoelOpbouw($value['id'], array());
                                                                if (count($doelOpbouw[0]->subdoelen) == 0) {
                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                                                                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                                                                    break;
                                                                }
                                                                for ($i = 1; $i <= count($doelOpbouw[0]->subdoelen); $i++) {
                                                                    if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                                                                    $subdoelSom = 0;
                                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $trededoel) {
                                                                        if (isset($trededoel->subdoelId)) {
                                                                            $subdoelSom += $cijfers[$trededoel->id . '_hoofd'];
                                                                        } else {
                                                                            $subdoelSom += $cijfers[$trededoel->id];
                                                                        }
                                                                    }
                                                                    $subdoelPercentage = round($subdoelSom / count($doelOpbouw[0]->subdoelen[$i]->trededoelen));
                                                                    if ($subdoelPercentage >= 80) continue;

                                                                    $doelKeuze = array();
                                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $key => $trededoel) {
                                                                        if (in_array($trededoel->id, $chosenSubdoelIds)) continue;
                                                                        $doelKeuze[$key]['id'] = $trededoel->id;
                                                                        $doelKeuze[$key]['naam'] = $trededoel->naam;
                                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                                        if (isset ($trededoel->subdoelId)) {
                                                                            $doelKeuze[$key]['subdoelId'] = $trededoel->subdoelId;
                                                                        }
                                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                                        if (isset($trededoel->subdoelId)) {
                                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id . '_hoofd']);
                                                                        } else {
                                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id]);
                                                                        }
                                                                    }
                                                                    $randMax = 0;
                                                                    foreach ($doelKeuze as $keuze) {
                                                                        $randMax += $keuze['kans'];
                                                                    }
                                                                    $random = (rand(1, $randMax));
                                                                    $som = 0;
                                                                    foreach ($doelKeuze as $key => $value) {
                                                                        $som += $value['kans'];
                                                                        if ($random <= $som) {
                                                                            if (!isset ($value['subdoelId'])) {
                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $value['id'];
                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $value['naam'];
                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $value['toestel'];
                                                                                $chosenSubdoelIds[] = $value['id'];
                                                                                break;
                                                                            }
                                                                            else {
                                                                                $doelOpbouw = $this->getDoelOpbouw($value['id'], array());
                                                                                if (count($doelOpbouw[0]->subdoelen) == 0) {
                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                                                                                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                                                                                    break;
                                                                                }
                                                                                for ($i = 1; $i <= count($doelOpbouw[0]->subdoelen); $i++) {
                                                                                    if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                                                                                    $subdoelSom = 0;
                                                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $trededoel) {
                                                                                        if (isset($trededoel->subdoelId)) {
                                                                                            $subdoelSom += $cijfers[$trededoel->id . '_hoofd'];
                                                                                        } else {
                                                                                            $subdoelSom += $cijfers[$trededoel->id];
                                                                                        }
                                                                                    }
                                                                                    $subdoelPercentage = round($subdoelSom / count($doelOpbouw[0]->subdoelen[$i]->trededoelen));
                                                                                    if ($subdoelPercentage >= 80) continue;

                                                                                    $doelKeuze = array();
                                                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $key => $trededoel) {
                                                                                        if (in_array($trededoel->id, $chosenSubdoelIds)) continue;
                                                                                        $doelKeuze[$key]['id'] = $trededoel->id;
                                                                                        $doelKeuze[$key]['naam'] = $trededoel->naam;
                                                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                                                        if (isset ($trededoel->subdoelId)) {
                                                                                            $doelKeuze[$key]['subdoelId'] = $trededoel->subdoelId;
                                                                                        }
                                                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                                                        if (isset($trededoel->subdoelId)) {
                                                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id . '_hoofd']);
                                                                                        } else {
                                                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id]);
                                                                                        }
                                                                                    }
                                                                                    $randMax = 0;
                                                                                    foreach ($doelKeuze as $keuze) {
                                                                                        $randMax += $keuze['kans'];
                                                                                    }
                                                                                    $random = (rand(1, $randMax));
                                                                                    $som = 0;
                                                                                    foreach ($doelKeuze as $key => $value) {
                                                                                        $som += $value['kans'];
                                                                                        if ($random <= $som) {
                                                                                            if (!isset ($value['subdoelId'])) {
                                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $value['id'];
                                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $value['naam'];
                                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $value['toestel'];
                                                                                                $chosenSubdoelIds[] = $value['id'];
                                                                                                break;
                                                                                            }
                                                                                            else {

                                                                                                $doelOpbouw = $this->getDoelOpbouw($value['id'], array());
                                                                                                if (count($doelOpbouw[0]->subdoelen) == 0) {
                                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                                                                                                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                                                                                                    break;
                                                                                                }
                                                                                                for ($i = 1; $i <= count($doelOpbouw[0]->subdoelen); $i++) {
                                                                                                    if (isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) continue;
                                                                                                    $subdoelSom = 0;
                                                                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $trededoel) {
                                                                                                        if (isset($trededoel->subdoelId)) {
                                                                                                            $subdoelSom += $cijfers[$trededoel->id . '_hoofd'];
                                                                                                        } else {
                                                                                                            $subdoelSom += $cijfers[$trededoel->id];
                                                                                                        }
                                                                                                    }
                                                                                                    $subdoelPercentage = round($subdoelSom / count($doelOpbouw[0]->subdoelen[$i]->trededoelen));
                                                                                                    if ($subdoelPercentage >= 80) continue;

                                                                                                    $doelKeuze = array();
                                                                                                    foreach ($doelOpbouw[0]->subdoelen[$i]->trededoelen as $key => $trededoel) {
                                                                                                        if (in_array($trededoel->id, $chosenSubdoelIds)) continue;
                                                                                                        $doelKeuze[$key]['id'] = $trededoel->id;
                                                                                                        $doelKeuze[$key]['naam'] = $trededoel->naam;
                                                                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                                                                        if (isset ($trededoel->subdoelId)) {
                                                                                                            $doelKeuze[$key]['subdoelId'] = $trededoel->subdoelId;
                                                                                                        }
                                                                                                        $doelKeuze[$key]['toestel'] = $trededoel->toestel;
                                                                                                        if (isset($trededoel->subdoelId)) {
                                                                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id . '_hoofd']);
                                                                                                        } else {
                                                                                                            $doelKeuze[$key]['kans'] = round(101 - $cijfers[$trededoel->id]);
                                                                                                        }
                                                                                                    }
                                                                                                    $randMax = 0;
                                                                                                    foreach ($doelKeuze as $keuze) {
                                                                                                        $randMax += $keuze['kans'];
                                                                                                    }
                                                                                                    $random = (rand(1, $randMax));
                                                                                                    $som = 0;
                                                                                                    foreach ($doelKeuze as $key => $value) {
                                                                                                        $som += $value['kans'];
                                                                                                        if ($random <= $som) {
                                                                                                            if (!isset ($value['subdoelId'])) {
                                                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $value['id'];
                                                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $value['naam'];
                                                                                                                $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $value['toestel'];
                                                                                                                $chosenSubdoelIds[] = $value['id'];
                                                                                                                break;
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!isset($chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'])) {
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelId'] = $doelOpbouw[0]->id;
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelNaam'] = $doelOpbouw[0]->naam;
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelToestel'] = $doelOpbouw[0]->toestel;
                    $chosenSubdoelIds[] = $doelOpbouw[0]->id;
                }
            }
        }
        return $chosenHoofdDoelen;
    }










    /**
     * @Route("/pdf/test/", name="testPdf")
     * @Method("GET")
     */
    public function testPDFCreation()
    {
        $pdf = new FPDF('L');
        $pdf->SetLeftMargin(10);
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial','B',16);
        $pdf->AddPage();
        $pdf->Cell(40,10,'Hello World!');
        return new Response($pdf->Output(), 200, array(
            'Content-Type' => 'application/pdf'));
    }
}