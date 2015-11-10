<?php
namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Controller\FpdfController as FPDF;
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
        return $this->render('inloggen/TrainingsplanIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'trainingsdata' => $trainingsdata,
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
        $trainingsdata->lesdatum = $lesdatum->format('d-m-Y');
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
     * @Route("/inloggen/selectie/{persoonId}/Trainingsplan/{groepId}/makeTrainingsPlan/{trainingId}/", name="makeTrainingsplan")
     * @Method({"POST"})
     */
    public function makeTrainingsplan($persoonId, $groepId, $trainingId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $seizoen = $this->getSeizoen();
        $toestellen = array('Sprong', 'Brug', 'Balk', 'Vloer');
        foreach ($_POST as $key => $value) {
            if (!isset ($chosenHoofdDoelen[$value]['begintoestel'])) {
                $toestelKey = rand(0,(count($toestellen)-1));
                $chosenHoofdDoelen[$value]['begintoestel'] = $toestellen[$toestelKey];
                unset ($toestellen[$toestelKey]);
            }
            $chosenHoofdDoelen[$value][$key] = $this->getTrainingsDoelPerTurnster($key, $seizoen);
        }
        var_dump($chosenHoofdDoelen);die;
    }

    private function getTrainingsDoelPerTurnster($turnsterId, $seizoen)
    {
        $doelenObject = $this->getDoelenVoorSeizoen($turnsterId, $seizoen);
        $doelen = $this->getDoelDetails($doelenObject);
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
                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelnaam'] = $doelOpbouw[0]->naam;
                    $chosenHoofdDoelen[$toestel][$doelNummer]['toestel'] = $doelOpbouw[0]->toestel;
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
                    if ($subdoelPercentage > 80) continue;

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
                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelnaam'] = $doelOpbouw[0]->naam;
                                    $chosenHoofdDoelen[$toestel][$doelNummer]['toestel'] = $doelOpbouw[0]->toestel;
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
                                    if ($subdoelPercentage > 80) continue;

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
                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelnaam'] = $doelOpbouw[0]->naam;
                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['toestel'] = $doelOpbouw[0]->toestel;
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
                                                    if ($subdoelPercentage > 80) continue;

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
                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelnaam'] = $doelOpbouw[0]->naam;
                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['toestel'] = $doelOpbouw[0]->toestel;
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
                                                                    if ($subdoelPercentage > 80) continue;

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
                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelnaam'] = $doelOpbouw[0]->naam;
                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['toestel'] = $doelOpbouw[0]->toestel;
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
                                                                                    if ($subdoelPercentage > 80) continue;

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
                                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['subdoelnaam'] = $doelOpbouw[0]->naam;
                                                                                                    $chosenHoofdDoelen[$toestel][$doelNummer]['toestel'] = $doelOpbouw[0]->toestel;
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
                                                                                                    if ($subdoelPercentage > 80) continue;

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