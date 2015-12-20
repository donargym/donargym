<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Aanwezigheid;
use AppBundle\Entity\Afmeldingen;
use AppBundle\Entity\Cijfers;
use AppBundle\Entity\Doelen;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FotoUpload;
use AppBundle\Entity\Functie;
use AppBundle\Entity\Groepen;
use AppBundle\Entity\Persoon;
use AppBundle\Entity\SeizoensDoelen;
use AppBundle\Entity\SelectieFoto;
use AppBundle\Entity\Stukje;
use AppBundle\Entity\SubDoelen;
use AppBundle\Entity\Trainingen;
use AppBundle\Entity\Trainingsdata;
use AppBundle\Entity\Vloermuziek;
use AppBundle\Entity\Wedstrijdkalender;
use AppBundle\Entity\Wedstrijduitslagen;
use AppBundle\Form\Type\ContactgegevensType;
use AppBundle\Form\Type\Email1Type;
use AppBundle\Form\Type\Email2Type;
use AppBundle\Form\Type\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Func;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Content;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\User\User;


class SelectieController extends BaseController
{

    public function __construct()
    {
    }

    /**
     * Creates a token usable in a form
     * @return string
     */
    protected function getToken()
    {
        $token = sha1(mt_rand());
        if (!isset($_SESSION['tokens'])) {
            $_SESSION['tokens'] = array($token => 1);
        } else {
            $_SESSION['tokens'][$token] = 1;
        }
        return $token;
    }

    /**
     * Check if a token is valid. Removes it from the valid tokens list
     * @param string $token The token
     * @return bool
     */
    protected function isTokenValid($token)
    {
        if (!empty($_SESSION['tokens'][$token])) {
            unset($_SESSION['tokens'][$token]);
            return true;
        }
        return false;
    }

    /**
     * @Route("/inloggen/selectie/", name="getSelectieIndexPage")
     * @Method({"GET"})
     * @Security("has_role('ROLE_TURNSTER')")
     */
    public function getSelectieIndexPage()
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        return $this->render('inloggen/selectieIndexPage.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    protected function getBasisUserGegevens($userObject)
    {
        $user = new \stdClass();
        $user->email = $userObject->getUsername();
        $user->email2 = $userObject->getEmail2();
        $user->straatnr = $userObject->getStraatnr();
        $user->postcode = $userObject->getPostcode();
        $user->plaats = $userObject->getPlaats();
        $user->tel1 = $userObject->getTel1();
        $user->tel2 = $userObject->getTel2();
        $user->tel3 = $userObject->getTel3();
        return ($user);
    }

    /**
     * @param $userObject
     * @return array
     */
    protected function getBasisPersoonsGegevens($userObject)
    {
        $personen = $userObject->getPersoon();
        $persoon = array();
        for ($i = 0; $i < count($personen); $i++) {
            $persoon[$i] = new \stdClass();
            $persoon[$i]->voornaam = $personen[$i]->getVoornaam();
            $persoon[$i]->achternaam = $personen[$i]->getAchternaam();
            $persoon[$i]->geboortedatum = $personen[$i]->getGeboortedatum();
            $persoon[$i]->id = $personen[$i]->getId();
            /** @var SelectieFoto $foto */
            $foto = $personen[$i]->getFoto();
            if (count($foto) > 0) {
                $persoon[$i]->foto = $foto->getLocatie();
            } else {
                $persoon[$i]->foto = 'uploads/selectiefotos/plaatje.jpg';
            }
            $groepen = $personen[$i]->getGroepen();
            $persoon[$i]->groepen = array();
            for ($j = 0; $j < count($groepen); $j++) {
                $persoon[$i]->groepen[$j] = new \stdClass();
                $persoon[$i]->groepen[$j]->naam = $groepen[$j]->getName();
            }
            $trainerFunctie = false;
            $functies = $personen[$i]->getFunctie();
            foreach ($functies as $functie) {
                if ($functie->getFunctie() != 'Turnster') {
                    $trainerFunctie = true;
                }
            }
            if ($trainerFunctie) {
                $persoon[$i]->functie = 'Trainer';
            }

        }
        return ($persoon);
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/editContactgegevens/", name="editContactgegevens")
     * @Method({"GET", "POST"})
     */
    public function editContactgegevens(Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $form = $this->createForm(new ContactgegevensType(), $userObject);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userObject);
            $em->flush();
            return $this->redirectToRoute('getSelectieIndexPage');
        } else {
            return $this->render('inloggen/editContactgegevens.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'persoon' => $persoon,
                'user' => $user,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/editEmail/", name="editEmail")
     * @Method({"GET", "POST"})
     */
    public function editEmail(Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $form = $this->createForm(new Email1Type(), $userObject);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userObject);
            $em->flush();
            return $this->redirectToRoute('getSelectieIndexPage');
        } else {
            return $this->render('inloggen/editEmail.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'persoon' => $persoon,
                'user' => $user,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/editEmail2/", name="editEmail2")
     * @Method({"GET", "POST"})
     */
    public function editEmail2(Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $form = $this->createForm(new Email2Type(), $userObject);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userObject);
            $em->flush();
            return $this->redirectToRoute('getSelectieIndexPage');
        } else {
            return $this->render('inloggen/editEmail2.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'persoon' => $persoon,
                'user' => $user,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/editPassword/", name="editPassword")
     * @Method({"GET", "POST"})
     */
    public function editPassword(Request $request)
    {
        $error = "";
        if ($request->getMethod() == 'POST') {
            if ($request->request->get('pass1') != $request->request->get('pass2')) {
                $error = "De wachtwoorden zijn niet gelijk";
            }
            if (strlen($request->request->get('pass1')) < 6) {
                $error = "Het wachtwoord moet minimaal 6 karakters bevatten";
            }
            if (strlen($request->request->get('pass1')) > 20) {
                $error = "Het wachtwoord mag maximaal 20 karakters bevatten";
            }
            if (empty($error)) {
                $userObject = $this->getUser();
                $password = $request->request->get('pass1');
                $encoder = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($userObject);
                $userObject->setPassword($encoder->encodePassword($password, $userObject->getSalt()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($userObject);
                $em->flush();

                return $this->redirectToRoute('getSelectieIndexPage');
            }
        }
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        return $this->render('inloggen/editPassword.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'error' => $error,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    protected function dayToDutch($time)
    {
        switch (date('N', $time)) {
            case 1:
                return 'Maandag';
                break;
            case 2:
                return 'Dinsdag';
                break;
            case 3:
                return 'Woensdag';
                break;
            case 4:
                return 'Donderdag';
                break;
            case 5:
                return 'Vrijdag';
                break;
            case 6:
                return 'Zaterdag';
                break;
            case 7:
                return 'Zondag';
                break;
        }
    }

    protected function getOnePersoon($userObject, $id, $afmelden = false, $page = null, $doelprijzen = false)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                $persoonItems = new \stdClass();
                $persoonItems->id = $persoon->getId();
                $persoonItems->voornaam = $persoon->getVoornaam();
                $persoonItems->achternaam = $persoon->getAchternaam();
                $foto = $persoon->getFoto();
                if ($foto == null) {
                    $persoonItems->foto = "plaatje.jpg";
                } else {
                    $persoonItems->foto = $foto->getLocatie();
                }
                $vloermuziek = $persoon->getVloermuziek();
                if ($vloermuziek == null) {
                    $persoonItems->vloermuziek = null;
                } else {
                    $persoonItems->vloermuziek = $vloermuziek->getLocatie();
                }
                $geboortedatum = $persoon->getGeboortedatum();
                $persoonItems->geboortedatum = date('d-m-Y', strtotime($geboortedatum));
                $persoonItems->categorie = $persoon->categorie(strtotime($geboortedatum));
                $functies = $persoon->getFunctie();
                $persoonItems->functies = array();
                for ($i = 0; $i < count($functies); $i++) {
                    $persoonItems->functies[$i] = new \stdClass();
                    /** @var Groepen $groep */
                    $groep = $functies[$i]->getGroep();
                    $persoonItems->functies[$i]->groepNaam = $groep->getName();
                    $persoonItems->functies[$i]->groepId = $groep->getId();
                    $persoonItems->functies[$i]->functie = $functies[$i]->getFunctie();
                    $persoonItems->functies[$i]->turnster = array();
                    $persoonItems->functies[$i]->wedstrijdkalenderItems = $this->getWedstrijdkalenderItems($groep->getId());
                    $stukje = $persoon->getStukje();
                    if (!$stukje) {
                        $stukje = new Stukje();
                        $persoon->setStukje($stukje);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($stukje);
                        $em->flush();
                    }
                    $persoonItems->stukje = $stukje->getAll();
                    if ($page == 'Index') {
                        $aanwezigheidPerPersoon = $this->getAanwezigheidPerPersoon($persoon, $groep->getId());
                        $persoonItems->functies[$i]->percentageAanwezig = $aanwezigheidPerPersoon->percentageAanwezig;
                        $persoonItems->functies[$i]->percentageKleur = $this->colorGenerator2($aanwezigheidPerPersoon->percentageAanwezig);
                        $persoonItems->functies[$i]->aantalAanwezig = $aanwezigheidPerPersoon->aantalAanwezig;
                        $persoonItems->functies[$i]->aantalTrainingen = $aanwezigheidPerPersoon->aantalTrainingen;
                    }
                    $countDoelenPrijzen = 0;
                    $countMedailles = 0;
                    $countBekers = 0;

                    $groepFuncties = $groep->getFuncties();
                    for ($j = 0; $j < count($groepFuncties); $j++) {
                        if ($groepFuncties[$j]->getFunctie() == 'Turnster') {
                            $persoonItems->functies[$i]->turnster[$j] = new \stdClass();
                            /** @var Persoon $turnster */
                            $turnster = $groepFuncties[$j]->getPersoon();
                            if ($turnster->getVoortgangTotaal() === null) {
                                $this->updateDoelCijfersInDatabase($turnster);
                            } if ($turnster->getLastUpdatedAtSeizoen() != $this->getSeizoen()) {
                                $turnster = $this->resetVoortgang($turnster);
                            } if (!$turnster->getUpdatedCijfersAt()) {
                                $turnster->setUpdatedCijfersAt(new \DateTime('now'));
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($turnster);
                                $em->flush();
                            }
                            $persoonItems->functies[$i]->turnster[$j]->percentageVoortgang = $turnster->getVoortgangTotaal();
                            $persoonItems->functies[$i]->turnster[$j]->updatedAt = $turnster->getUpdatedCijfersAt();
                            $persoonItems->functies[$i]->turnster[$j]->percentageVoortgangKleur = $this->colorGenerator($persoonItems->functies[$i]->turnster[$j]->percentageVoortgang);
                            if ($page == 'Index') {
                                $aantalAanwezig = 0;
                                $aantalTrainingen = 0;
                                $totaalAanwezigheid = $turnster->getAanwezigheid();
                                for ($counter = (count($totaalAanwezigheid) - 1); $counter >= 0; $counter--) {
                                    $check = false;
                                    /** @var Trainingsdata $trainingsdatum */
                                    $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
                                    $lesdatum = $trainingsdatum->getLesdatum();
                                    /** @var Trainingen $training */
                                    $training = $trainingsdatum->getTrainingen();
                                    /** @var Groepen $trainingGroep */
                                    $trainingGroep = $training->getGroep();
                                    if ($lesdatum->getTimestamp() <= time() && $trainingGroep->getId() == $persoonItems->functies[$i]->groepId) {
                                        if (date('m', time()) < '08') {
                                            if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                                ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                            ) {
                                                $check = true;
                                            } else {
                                                break;
                                            }
                                        } else {
                                            if ($lesdatum->format('Y') == date('Y', time())) {
                                                if ($lesdatum->format('m') < '08') {
                                                    break;
                                                } else {
                                                    $check = true;
                                                }
                                            }
                                        }
                                    }
                                    if ($check) {
                                        $aantalTrainingen++;
                                        if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                                            $aantalAanwezig++;
                                        }
                                    }
                                }
                                if ($aantalTrainingen != 0) {
                                    $persoonItems->functies[$i]->turnster[$j]->percentageAanwezig = 100 * $aantalAanwezig / $aantalTrainingen;
                                } else {
                                    $persoonItems->functies[$i]->turnster[$j]->percentageAanwezig = 100;
                                }
                                $persoonItems->functies[$i]->turnster[$j]->aantalAanwezig = $aantalAanwezig;
                                $persoonItems->functies[$i]->turnster[$j]->aantalTrainingen = $aantalTrainingen;
                                $persoonItems->functies[$i]->turnster[$j]->percentageKleur = $this->colorGenerator($persoonItems->functies[$i]->turnster[$j]->percentageAanwezig);
                            }

                            $persoonItems->functies[$i]->turnster[$j]->voornaam = $turnster->getVoornaam();
                            $persoonItems->functies[$i]->turnster[$j]->achternaam = $turnster->getAchternaam();
                            $persoonItems->functies[$i]->turnster[$j]->id = $turnster->getId();
                            $geboortedatum = $turnster->getGeboortedatum();
                            $turnsterUser = $turnster->getUser();
                            $persoonItems->functies[$i]->turnster[$j]->email = $turnsterUser->getUsername();
                            $persoonItems->functies[$i]->turnster[$j]->email2 = $turnsterUser->getEmail2();
                            $persoonItems->functies[$i]->turnster[$j]->straatNr = $turnsterUser->getStraatnr();
                            $persoonItems->functies[$i]->turnster[$j]->postcode = $turnsterUser->getPostcode();
                            $persoonItems->functies[$i]->turnster[$j]->plaats = $turnsterUser->getPlaats();
                            $persoonItems->functies[$i]->turnster[$j]->tel1 = $turnsterUser->getTel1();
                            $persoonItems->functies[$i]->turnster[$j]->tel2 = $turnsterUser->getTel2();
                            $persoonItems->functies[$i]->turnster[$j]->tel3 = $turnsterUser->getTel3();
                            $persoonItems->functies[$i]->turnster[$j]->geboortedatum = date('d-m-Y', strtotime($geboortedatum));
                            $response = $this->getDoelCijfersForTurnster($turnster->getId());
                            $persoonItems->functies[$i]->turnster[$j]->cijfers = $response[0];
                            $persoonItems->functies[$i]->turnster[$j]->kleuren = $response[1];
                            $persoonItems->functies[$i]->turnster[$j]->voortgang = $response[2];
                            $persoonItems->functies[$i]->turnster[$j]->doelen = $response[3];
                            $doelIds = $response[4];
                            if ($doelprijzen) {
                                $persoonItems->functies[$i]->turnster[$j]->doelenPrijzen = array();
                                $doelCounter = 0;
                                foreach ($doelIds as $doelId) {
                                    $check = false;
                                    if ($result = $this->getDoelVoorPrijzenTachtigProcent($doelId, $turnster->getId())) {
                                        $persoonItems->functies[$i]->turnster[$j]->doelenPrijzen[$doelCounter] = new \stdClass();
                                        $persoonItems->functies[$i]->turnster[$j]->doelenPrijzen[$doelCounter]->tachtigProcent = $result;
                                        $countDoelenPrijzen++;
                                        $countMedailles++;
                                        $check = true;
                                    }
                                    if ($result = $this->getDoelVoorPrijzenNegentigProcent($doelId, $turnster->getId())) {
                                        $persoonItems->functies[$i]->turnster[$j]->doelenPrijzen[$doelCounter]->negentigProcent = $result;
                                        $countDoelenPrijzen++;
                                        $countBekers++;
                                        $check = true;
                                    }
                                    if ($check) $doelCounter++;
                                }
                            }

                        } elseif ($groepFuncties[$j]->getFunctie() == 'Trainer') {
                            $persoonItems->functies[$i]->trainer[$j] = new \stdClass();
                            /** @var Persoon $trainer */
                            $trainer = $groepFuncties[$j]->getPersoon();

                            $aantalAanwezig = 0;
                            $aantalTrainingen = 0;
                            $totaalAanwezigheid = $trainer->getAanwezigheid();
                            for ($counter = (count($totaalAanwezigheid) - 1); $counter >= 0; $counter--) {
                                $check = false;
                                /** @var Trainingsdata $trainingsdatum */
                                $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
                                $lesdatum = $trainingsdatum->getLesdatum();
                                /** @var Trainingen $training */
                                $training = $trainingsdatum->getTrainingen();
                                /** @var Groepen $trainingGroep */
                                $trainingGroep = $training->getGroep();
                                if ($lesdatum->getTimestamp() <= time() && $trainingGroep->getId() == $persoonItems->functies[$i]->groepId) {
                                    if (date('m', time()) < '08') {
                                        if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                        ) {
                                            $check = true;
                                        } else {
                                            break;
                                        }
                                    } else {
                                        if ($lesdatum->format('Y') == date('Y', time())) {
                                            if ($lesdatum->format('m') < '08') {
                                                break;
                                            } else {
                                                $check = true;
                                            }
                                        }
                                    }
                                }
                                if ($check) {
                                    $aantalTrainingen++;
                                    if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                                        $aantalAanwezig++;
                                    }
                                }
                            }
                            if ($aantalTrainingen != 0) {
                                $persoonItems->functies[$i]->trainer[$j]->percentageAanwezig = 100 * $aantalAanwezig / $aantalTrainingen;
                            } else {
                                $persoonItems->functies[$i]->trainer[$j]->percentageAanwezig = 100;
                            }
                            $persoonItems->functies[$i]->trainer[$j]->aantalAanwezig = $aantalAanwezig;
                            $persoonItems->functies[$i]->trainer[$j]->aantalTrainingen = $aantalTrainingen;
                            $persoonItems->functies[$i]->trainer[$j]->percentageKleur = $this->colorGenerator($persoonItems->functies[$i]->trainer[$j]->percentageAanwezig);
                            $persoonItems->functies[$i]->trainer[$j]->voornaam = $trainer->getVoornaam();
                            $persoonItems->functies[$i]->trainer[$j]->achternaam = $trainer->getAchternaam();
                            $persoonItems->functies[$i]->trainer[$j]->id = $trainer->getId();
                            $geboortedatum = $trainer->getGeboortedatum();
                            $trainerUser = $trainer->getUser();
                            $persoonItems->functies[$i]->trainer[$j]->email = $trainerUser->getUsername();
                            $persoonItems->functies[$i]->trainer[$j]->email2 = $trainerUser->getEmail2();
                            $persoonItems->functies[$i]->trainer[$j]->straatNr = $trainerUser->getStraatnr();
                            $persoonItems->functies[$i]->trainer[$j]->postcode = $trainerUser->getPostcode();
                            $persoonItems->functies[$i]->trainer[$j]->plaats = $trainerUser->getPlaats();
                            $persoonItems->functies[$i]->trainer[$j]->tel1 = $trainerUser->getTel1();
                            $persoonItems->functies[$i]->trainer[$j]->tel2 = $trainerUser->getTel2();
                            $persoonItems->functies[$i]->trainer[$j]->tel3 = $trainerUser->getTel3();
                            $persoonItems->functies[$i]->trainer[$j]->geboortedatum = date('d-m-Y', strtotime($geboortedatum));
                        } elseif ($groepFuncties[$j]->getFunctie() == 'Assistent-Trainer') {
                            $persoonItems->functies[$i]->assistent[$j] = new \stdClass();
                            /** @var Persoon $assistent */
                            $assistent = $groepFuncties[$j]->getPersoon();

                            $aantalAanwezig = 0;
                            $aantalTrainingen = 0;
                            $totaalAanwezigheid = $assistent->getAanwezigheid();
                            for ($counter = (count($totaalAanwezigheid) - 1); $counter >= 0; $counter--) {
                                $check = false;
                                /** @var Trainingsdata $trainingsdatum */
                                $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
                                $lesdatum = $trainingsdatum->getLesdatum();
                                /** @var Trainingen $training */
                                $training = $trainingsdatum->getTrainingen();
                                /** @var Groepen $trainingGroep */
                                $trainingGroep = $training->getGroep();
                                if ($lesdatum->getTimestamp() <= time() && $trainingGroep->getId() == $persoonItems->functies[$i]->groepId) {
                                    if (date('m', time()) < '08') {
                                        if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                        ) {
                                            $check = true;
                                        } else {
                                            break;
                                        }
                                    } else {
                                        if ($lesdatum->format('Y') == date('Y', time())) {
                                            if ($lesdatum->format('m') < '08') {
                                                break;
                                            } else {
                                                $check = true;
                                            }
                                        }
                                    }
                                }
                                if ($check) {
                                    $aantalTrainingen++;
                                    if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                                        $aantalAanwezig++;
                                    }
                                }
                            }
                            if ($aantalTrainingen != 0) {
                                $persoonItems->functies[$i]->assistent[$j]->percentageAanwezig = 100 * $aantalAanwezig / $aantalTrainingen;
                            } else {
                                $persoonItems->functies[$i]->assistent[$j]->percentageAanwezig = 100;
                            }
                            $persoonItems->functies[$i]->assistent[$j]->aantalAanwezig = $aantalAanwezig;
                            $persoonItems->functies[$i]->assistent[$j]->aantalTrainingen = $aantalTrainingen;
                            $persoonItems->functies[$i]->assistent[$j]->percentageKleur = $this->colorGenerator($persoonItems->functies[$i]->assistent[$j]->percentageAanwezig);
                            $persoonItems->functies[$i]->assistent[$j]->voornaam = $assistent->getVoornaam();
                            $persoonItems->functies[$i]->assistent[$j]->achternaam = $assistent->getAchternaam();
                            $persoonItems->functies[$i]->assistent[$j]->id = $assistent->getId();
                            $geboortedatum = $assistent->getGeboortedatum();
                            $assistentUser = $assistent->getUser();
                            $persoonItems->functies[$i]->assistent[$j]->email = $assistentUser->getUsername();
                            $persoonItems->functies[$i]->assistent[$j]->email2 = $assistentUser->getEmail2();
                            $persoonItems->functies[$i]->assistent[$j]->straatNr = $assistentUser->getStraatnr();
                            $persoonItems->functies[$i]->assistent[$j]->postcode = $assistentUser->getPostcode();
                            $persoonItems->functies[$i]->assistent[$j]->plaats = $assistentUser->getPlaats();
                            $persoonItems->functies[$i]->assistent[$j]->tel1 = $assistentUser->getTel1();
                            $persoonItems->functies[$i]->assistent[$j]->tel2 = $assistentUser->getTel2();
                            $persoonItems->functies[$i]->assistent[$j]->tel3 = $assistentUser->getTel3();
                            $persoonItems->functies[$i]->assistent[$j]->geboortedatum = date('d-m-Y', strtotime($geboortedatum));
                        }
                    }
                    $persoonItems->functies[$i]->doelenPrijzenAantal = $countDoelenPrijzen;
                    $persoonItems->functies[$i]->bekersAantal = $countBekers;
                    $persoonItems->functies[$i]->medaillesAantal = $countMedailles;
                }
                /** @var Trainingen $trainingen */
                if ($page == 'Index' || $afmelden) {
                    $trainingen = $persoon->getTrainingen();
                    $persoonItems->trainingen = array();
                    for ($i = 0; $i < count($trainingen); $i++) {
                        $persoonItems->trainingen[$i] = new \stdClass();
                        $persoonItems->trainingen[$i]->trainingId = $trainingen[$i]->getId();
                        $persoonItems->trainingen[$i]->dag = $trainingen[$i]->getDag();
                        $groep = $trainingen[$i]->getGroep();
                        $persoonItems->trainingen[$i]->groepId = $groep->getId();
                        $persoonItems->trainingen[$i]->tijdTot = $trainingen[$i]->getTijdtot();
                        $persoonItems->trainingen[$i]->tijdVan = $trainingen[$i]->getTijdvan();
                        $persoonItems->trainingen[$i]->trainingsdata = array();
                        $trainingsdata = $trainingen[$i]->getTrainingsdata();
                        if ($afmelden) {
                            $counter = 0;
                            $aanwezigheid = $persoon->getAanwezigheid();
                            for ($j = (count($trainingsdata) - 1); $j >= 0; $j--) {
                                $lesdatum = $trainingsdata[$j]->getLesdatum();
                                $timestamp = $lesdatum->getTimestamp();
                                $timestampPlusDag = ((int)$timestamp + 86400);
                                if (($timestampPlusDag) > time()) {
                                    $persoonItems->trainingen[$i]->trainingsdata[$j] = new \stdClass();
                                    $persoonItems->trainingen[$i]->trainingsdata[$j]->id = $trainingsdata[$j]->getId();
                                    $persoonItems->trainingen[$i]->trainingsdata[$j]->lesdatum = $lesdatum->format('d-m-Y');
                                    /** @var Aanwezigheid $aanwezig */
                                    foreach ($aanwezigheid as $aanwezig) {
                                        if ($aanwezig->getTrainingsdata() == $trainingsdata[$j]) {
                                            $persoonItems->trainingen[$i]->trainingsdata[$j]->afmelding = $aanwezig->getAanwezig();
                                        }
                                    }
                                    $counter++;
                                    if ($counter == 10) {
                                        $j = 0;
                                        $counter++;
                                    }
                                }
                            }
                            if ($counter < 10) {
                                if (count($trainingsdata) == 0) {
                                    for ($try = 0; $try < 7; $try++) {
                                        $dag = $this->dayToDutch((time() + ($try * 86400)));
                                        if ($dag == $persoonItems->trainingen[$i]->dag) {
                                            $lesdatum = date('Y-m-d', (time() + ($try * 86400) - 604800));
                                            $try = 7;
                                        }
                                    }
                                } else {
                                    $j = (count($trainingsdata) - 1);
                                    $lesdatum = $trainingsdata[$j]->getLesdatum();
                                    $lesdatum = $lesdatum->format('Y-m-d');
                                }
                                $week = (604800 + 12 * 3600);
                                for ($counter; $counter < 10; $counter++) {
                                    $lesdatum = date('Y-m-d', (strtotime($lesdatum) + $week));
                                    $lesdatumForDb = \DateTime::createFromFormat('Y-m-d', $lesdatum);
                                    $newLesdatum = new Trainingsdata();
                                    $newLesdatum->setLesdatum($lesdatumForDb);
                                    $newLesdatum->setTrainingen($trainingen[$i]);
                                    $em = $this->getDoctrine()->getManager();
                                    $em->persist($newLesdatum);
                                    $em->flush();
                                }
                            }
                            $persoonItems->trainingen[$i]->trainingsdata = array_reverse($persoonItems->trainingen[$i]->trainingsdata);
                        } else {
                            $counter = 0;
                            $aantalTrainingen = 0;
                            $aantalAanwezig = 0;
                            $aanwezigheid = $persoon->getAanwezigheid();
                            for ($j = (count($trainingsdata) - 4); $j >= 0; $j--) {
                                $lesdatum = $trainingsdata[$j]->getLesdatum();
                                if (strtotime($lesdatum->format('d-m-Y')) <= time()) {
                                    for ($k = (count($aanwezigheid) - 1); $k >= 0; $k--) {
                                        $check = false;
                                        if (date('m', time()) < '08') {
                                            if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                                ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                            ) {
                                                $check = true;
                                            } else {
                                                break;
                                            }
                                        } else {
                                            if ($lesdatum->format('Y') == date('Y', time())) {
                                                if ($lesdatum->format('m') < '08') {
                                                    break;
                                                } else {
                                                    $check = true;
                                                }
                                            }
                                        }
                                        if ($check) {
                                            if ($aanwezigheid[$k]->getTrainingsdata() == $trainingsdata[$j]) {
                                                $aantalTrainingen++;
                                                if ($counter < 7) {
                                                    $persoonItems->trainingen[$i]->trainingsdata[$j] = new \stdClass();
                                                    $persoonItems->trainingen[$i]->trainingsdata[$j]->id = $trainingsdata[$j]->getId();
                                                    $persoonItems->trainingen[$i]->trainingsdata[$j]->lesdatum = $lesdatum->format('d-m-Y');
                                                    $persoonItems->trainingen[$i]->trainingsdata[$j]->aanwezigheid = $aanwezigheid[$k]->getAanwezig();
                                                    $counter++;
                                                }
                                                if (strtolower($aanwezigheid[$k]->getAanwezig()) == 'x') {
                                                    $aantalAanwezig++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $persoonItems->trainingen[$i]->trainingsdata = array_reverse($persoonItems->trainingen[$i]->trainingsdata);
                            if ($aantalTrainingen == 0) {
                                $persoonItems->trainingen[$i]->percentageAanwezig = 100;
                            } else {
                                $persoonItems->trainingen[$i]->percentageAanwezig = (100 * ($aantalAanwezig / $aantalTrainingen));
                            }
                            $persoonItems->trainingen[$i]->aantalAanwezig = $aantalAanwezig;
                            $persoonItems->trainingen[$i]->aantalTrainingen = $aantalTrainingen;
                            $persoonItems->trainingen[$i]->percentageKleur = $this->colorGenerator($persoonItems->trainingen[$i]->percentageAanwezig);
                        }
                    }
                }
                foreach ($persoonItems->functies as $functie) {
                    usort($functie->turnster, function ($a, $b) {
                        $t1 = strtotime($a->geboortedatum);
                        $t2 = strtotime($b->geboortedatum);
                        return $t1 - $t2;
                    });
                }
                return ($persoonItems);
            }
        }
    }

    private function getDoelVoorPrijzenTachtigProcent($doelId, $turnsterId)
    {
        $seizoen = $this->getSeizoen();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT seizoensdoelen
            FROM AppBundle:SeizoensDoelen seizoensdoelen
            WHERE seizoensdoelen.seizoen = :seizoen
            AND seizoensdoelen.doel = :doel
            AND seizoensdoelen.cijfer >= :cijfer
            AND seizoensdoelen.tachtigProcent IS NULL
            AND seizoensdoelen.persoon = :persoon')
            ->setParameter('seizoen', $seizoen)
            ->setParameter('doel', $doelId)
            ->setParameter('cijfer', 80)
            ->setParameter('persoon', $turnsterId);
        /** @var SeizoensDoelen $result */
        $result = $query->setMaxResults(1)->getOneOrNullResult();
        if (count($result) == 1) {
            $doelPrijs = new \stdClass();
            $helper = $result->getDoel();
            $doelPrijs->naam = $helper->getNaam();
            $doelPrijs->toestel = $helper->getToestel();
            $doelPrijs->cijfer = $result->getCijfer();
            return $doelPrijs;
        } else {
            return false;
        }
    }

    private function getDoelVoorPrijzenNegentigProcent($doelId, $turnsterId)
    {
        $seizoen = $this->getSeizoen();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT seizoensdoelen
            FROM AppBundle:SeizoensDoelen seizoensdoelen
            WHERE seizoensdoelen.seizoen = :seizoen
            AND seizoensdoelen.doel = :doel
            AND seizoensdoelen.cijfer >= :cijfer
            AND seizoensdoelen.negentigProcent IS NULL
            AND seizoensdoelen.persoon = :persoon')
            ->setParameter('seizoen', $seizoen)
            ->setParameter('doel', $doelId)
            ->setParameter('cijfer', 90)
            ->setParameter('persoon', $turnsterId);
        /** @var SeizoensDoelen $result */
        $result = $query->setMaxResults(1)->getOneOrNullResult();
        if (count($result) == 1) {
            $doel = new \stdClass();
            $helper = $result->getDoel();
            $doel->naam = $helper->getNaam();
            $doel->toestel = $helper->getToestel();
            $doel->cijfer = $result->getCijfer();
            return $doel;
        } else {
            return false;
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/addKalenderItem/{persoonId}/{groepId}/", name="addKalenderItem")
     * @Method({"GET", "POST"})
     */
    public function addWedstrijdkalenderItems($persoonId, $groepId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $groepsnaam = $response['groep']->getName();
            $groepTurnsters = $response['groep']->getPeople();
            $turnsters = array();
            for ($i = 0; $i < count($groepTurnsters); $i++) {
                $functies = $groepTurnsters[$i]->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Functie $functie */
                    if ($functie->getGroep() == $response['groep'] && $functie->getFunctie() == 'Turnster') {
                        $turnsters[$i] = new \stdClass();
                        $turnsters[$i]->naam = $groepTurnsters[$i]->getVoornaam() . ' ' . $groepTurnsters[$i]->getAchternaam();
                        $turnsters[$i]->id = $groepTurnsters[$i]->getId();
                        $turnsters[$i]->geboortedatum = $groepTurnsters[$i]->getGeboortedatum();
                        $turnsters[$i]->selected = false;
                    }
                }
            }

            usort($turnsters, function($a, $b)
            {
                if ($a->geboortedatum == $b->geboortedatum)
                {
                    return 0;
                }
                else if ($a->geboortedatum < $b->geboortedatum)
                {
                    return -1;
                }
                else {
                    return 1;
                }
            });

            if ($request->getMethod() == 'POST') {
                $postedToken = $request->request->get('token');
                if (!empty($postedToken)) {
                    if ($this->isTokenValid($postedToken)) {
                        $em = $this->getDoctrine()->getManager();
                        $wedstrijdkalenderItem = new Wedstrijdkalender();
                        $wedstrijdkalenderItem->setWedstrijdnaam($request->get('wedstrijdnaam'));
                        $datum = \DateTime::createFromFormat('Y-m-d', $request->get('datum'));
                        $wedstrijdkalenderItem->setDatum($datum);
                        $wedstrijdkalenderItem->setGroep($response['groep']);
                        $wedstrijdkalenderItem->setLocatie($request->get('locatie'));
                        $wedstrijdkalenderItem->setTijden($request->get('tijden'));
                        foreach ($turnsters as $turnster) {
                            if ($request->get('turnsters_' . $turnster->id)) {
                                $query = $em->createQuery(
                                'SELECT persoon
                                FROM AppBundle:Persoon persoon
                                WHERE persoon.id = :id')
                                    ->setParameter('id', $turnster->id);
                                $persoon = $query->setMaxResults(1)->getOneOrNullResult();
                                $wedstrijdkalenderItem->addPersoon($persoon);
                            }
                        }
                        $em->persist($wedstrijdkalenderItem);
                        $em->flush();
                        return $this->redirectToRoute('showPersoon', array(
                            'id' => $persoonId
                        ));
                    }
                }
            }
            $token = $this->getToken();
            return $this->render('inloggen/addEditKalendarItems.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'persoon' => $persoon,
                'user' => $user,
                'persoonItems' => $persoonItems,
                'wedstrijdLinkItems' => $this->groepItems,
                'groep' => $groepsnaam,
                'groepId' => $groepId,
                'persoonId' => $persoonId,
                'token' => $token,
                'action' => 'add',
                'turnsters' => $turnsters,
            ));
        } else {
            return $this->render('error/NotAuthorized.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/editKalenderItem/{persoonId}/{groepId}/{kalenderItemId}", name="editKalenderItem")
     * @Method({"GET", "POST"})
     */
    public function editWedstrijdkalenderItems($persoonId, $groepId, $kalenderItemId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT wedstrijdkalender
                FROM AppBundle:Wedstrijdkalender wedstrijdkalender
                WHERE wedstrijdkalender.id = :id')
                ->setParameter('id', $kalenderItemId);
            /** @var Wedstrijdkalender $kalenderItem */
            $kalenderItem = $query->setMaxResults(1)->getOneOrNullResult();
            $wedstrijdnaam = $kalenderItem->getWedstrijdnaam();
            $datum = $kalenderItem->getDatum();
            $datum = $datum->format('Y-m-d');
            $tijden = $kalenderItem->getTijden();
            $locatie = $kalenderItem->getLocatie();
            $selectedTurnsters = $kalenderItem->getPersoon();
            $groepsnaam = $response['groep']->getName();
            $groepTurnsters = $response['groep']->getPeople();
            $turnsters = array();
            for ($i = 0; $i < count($groepTurnsters); $i++) {
                $functies = $groepTurnsters[$i]->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Functie $functie */
                    if ($functie->getGroep() == $response['groep'] && $functie->getFunctie() == 'Turnster') {
                        $turnsters[$groepTurnsters[$i]->getId()] = new \stdClass();
                        $turnsters[$groepTurnsters[$i]->getId()]->naam = $groepTurnsters[$i]->getVoornaam() . ' ' . $groepTurnsters[$i]->getAchternaam();
                        $turnsters[$groepTurnsters[$i]->getId()]->id = $groepTurnsters[$i]->getId();
                        $turnsters[$groepTurnsters[$i]->getId()]->geboortedatum = $groepTurnsters[$i]->getGeboortedatum();
                        $turnsters[$groepTurnsters[$i]->getId()]->selected = false;
                    }
                }
            }
            foreach ($selectedTurnsters as $selectedTurnster) {
                $turnsters[$selectedTurnster->getId()]->selected = true;
            }
            usort($turnsters, function($a, $b)
            {
                if ($a->geboortedatum == $b->geboortedatum)
                {
                    return 0;
                }
                else if ($a->geboortedatum < $b->geboortedatum)
                {
                    return -1;
                }
                else {
                    return 1;
                }
            });
            if ($request->getMethod() == 'POST') {
                $postedToken = $request->request->get('token');
                if (!empty($postedToken)) {
                    if ($this->isTokenValid($postedToken)) {
                        $kalenderItem->setWedstrijdnaam($request->get('wedstrijdnaam'));
                        $datum = \DateTime::createFromFormat('Y-m-d', $request->get('datum'));
                        $kalenderItem->setDatum($datum);
                        $kalenderItem->setGroep($response['groep']);
                        $kalenderItem->setLocatie($request->get('locatie'));
                        $kalenderItem->setTijden($request->get('tijden'));
                        foreach ($selectedTurnsters as $selectedTurnster) {
                            $kalenderItem->removePersoon($selectedTurnster);
                        }
                        foreach ($turnsters as $turnster) {
                            if ($request->get('turnsters_' . $turnster->id)) {
                                $query = $em->createQuery(
                                    'SELECT persoon
                                FROM AppBundle:Persoon persoon
                                WHERE persoon.id = :id')
                                    ->setParameter('id', $turnster->id);
                                $persoon = $query->setMaxResults(1)->getOneOrNullResult();
                                $kalenderItem->addPersoon($persoon);
                            }
                        }
                        $em->persist($kalenderItem);
                        $em->flush();
                        return $this->redirectToRoute('showPersoon', array(
                            'id' => $persoonId
                        ));
                    }
                }
            }
            $token = $this->getToken();
            return $this->render('inloggen/addEditKalendarItems.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'persoon' => $persoon,
                'user' => $user,
                'persoonItems' => $persoonItems,
                'wedstrijdLinkItems' => $this->groepItems,
                'groep' => $groepsnaam,
                'groepId' => $groepId,
                'persoonId' => $persoonId,
                'token' => $token,
                'action' => 'edit',
                'turnsters' => $turnsters,
                'wedstrijdnaam' => $wedstrijdnaam,
                'datum' => $datum,
                'tijden' => $tijden,
                'locatie' => $locatie,
                'kalenderItemId' => $kalenderItemId,
            ));
        } else {
            return $this->render('error/NotAuthorized.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/removeKalenderItem/{persoonId}/{groepId}/{kalenderItemId}/", name="removeKalenderItem")
     * @Method({"GET"})
     */
    public function removeWedstrijdkalenderItems($persoonId, $groepId, $kalenderItemId, Request $request)
    {
        $userObject = $this->getUser();
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT wedstrijdkalender
                FROM AppBundle:Wedstrijdkalender wedstrijdkalender
                WHERE wedstrijdkalender.id = :id')
                ->setParameter('id', $kalenderItemId);
            $kalenderItem = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($kalenderItem);
            $em->flush();
            return $this->redirectToRoute('showPersoon', array(
                'id' => $persoonId
            ));
        }
    }

    private function getWedstrijdkalenderItems($groepId)
    {
        $datum = new \DateTime('now');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT wedstrijdkalender
            FROM AppBundle:Wedstrijdkalender wedstrijdkalender
            WHERE wedstrijdkalender.groep = :id
            AND wedstrijdkalender.datum >= :datum
            ORDER BY wedstrijdkalender.datum ASC')
            ->setParameter('id', $groepId)
            ->setParameter('datum', $datum);
        $wedstrijdkalenderObjects = $query->getResult();
        $wedstrijdkalender = array();
        for ($i=0; $i<count($wedstrijdkalenderObjects); $i++) {
            $wedstrijdkalender[$i] = $wedstrijdkalenderObjects[$i]->getAll();
            $wedstrijdkalender[$i]->turnsters = array();
            $turnsters = $wedstrijdkalenderObjects[$i]->getPersoon();
            for ($j = 0; $j < count($turnsters); $j++) {
                $wedstrijdkalender[$i]->turnsters[$j] = new \stdClass();
                $wedstrijdkalender[$i]->turnsters[$j]->id = $turnsters[$j]->getId();
                $wedstrijdkalender[$i]->turnsters[$j]->voornaam = $turnsters[$j]->getVoornaam();
                $wedstrijdkalender[$i]->turnsters[$j]->achternaam = $turnsters[$j]->getAchternaam();
                if ($j != count($turnsters) - 1) {
                    $wedstrijdkalender[$i]->turnsters[$j]->achternaam .= ', ';
                }
            }
        }
        return $wedstrijdkalender;
    }

    protected function updateDoelCijfersInDatabase($turnster)
    {
        /** @var Persoon $turnster */
        $seizoen = $this->getSeizoen();
        $doelenObject = $this->getDoelenVoorSeizoen($turnster->getId(), $seizoen);
        $doelen = $this->getDoelDetails($doelenObject);
        $doelen = $doelen[0];
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
                    if ($result = $this->getPercentages($extraDoel, $cijfers, $turnster->getId())) {
                        $cijfers = $result;
                        unset ($reveseExtraDoelen[$id]);
                        continue;
                    }
                    $repeat = true;
                }
            }
            $cijfers = $this->getPercentages($doelOpbouw, $cijfers, $turnster->getId());
            foreach ($cijfers as $id => $percentage) {
                $kleuren[$id] = $this->colorGenerator($percentage);
            }
        }
        $voortgang = $this->getPercentageVoortgangTotaal($doelen, $cijfers);

        $turnster->setVoortgangSprong($voortgang['Sprong']);
        $turnster->setVoortgangBrug($voortgang['Brug']);
        $turnster->setVoortgangBalk($voortgang['Balk']);
        $turnster->setVoortgangVloer($voortgang['Vloer']);
        $turnster->setVoortgangTotaal($voortgang['totaal']);
        $turnster->setLastUpdatedAtSeizoen($this->getSeizoen());
        $turnster->setUpdatedCijfersAt(new \DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($turnster);
        $em->flush();
    }

    protected function getAanwezigheidPerPersoon($persoon, $groepId)
    {
        $aanwezigheid = new \stdClass();
        /** @var Persoon $persoon */
        $aanwezigheid->aantalAanwezig = 0;
        $aanwezigheid->aantalTrainingen = 0;
        $totaalAanwezigheid = $persoon->getAanwezigheid();
        for ($counter = (count($totaalAanwezigheid) - 1); $counter >= 0; $counter--) {
            $check = false;
            /** @var Trainingsdata $trainingsdatum */
            $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
            $lesdatum = $trainingsdatum->getLesdatum();
            /** @var Trainingen $training */
            $training = $trainingsdatum->getTrainingen();
            /** @var Groepen $trainingGroep */
            $trainingGroep = $training->getGroep();
            if ($lesdatum->getTimestamp() <= time() && $trainingGroep->getId() == $groepId) {
                if (date('m', time()) < '08') {
                    if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                        ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                    ) {
                        $check = true;
                    } else {
                        break;
                    }
                } else {
                    if ($lesdatum->format('Y') == date('Y', time())) {
                        if ($lesdatum->format('m') < '08') {
                            break;
                        } else {
                            $check = true;
                        }
                    }
                }
            }
            if ($check) {
                $aanwezigheid->aantalTrainingen++;
                if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                    $aanwezigheid->aantalAanwezig++;
                }
            }
        }
        if ($aanwezigheid->aantalTrainingen != 0) {
            $aanwezigheid->percentageAanwezig = 100 * $aanwezigheid->aantalAanwezig / $aanwezigheid->aantalTrainingen;
        } else {
            $aanwezigheid->percentageAanwezig = 100;
        }
        $aanwezigheid->percentageKleur = $this->colorGenerator($aanwezigheid->percentageAanwezig);
        return $aanwezigheid;
    }

    protected function getPersoonObject($userObject, $id)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                return $persoon;
            }
        }
    }

    protected function ColorGenerator2($percentage)
    {
        if ($percentage >= 100) {
            return 'rgba(0,255,0,0.4)';
        } //Green
        elseif ($percentage >= 99) {
            return 'rgba(17,255,0,0.4)';
        } elseif ($percentage >= 97) {
            return 'rgba(34,255,0,0.4)';
        } elseif ($percentage >= 96) {
            return 'rgba(51,255,0,0.4)';
        } elseif ($percentage >= 94) {
            return 'rgba(68,255,0,0.4)';
        } elseif ($percentage >= 93) {
            return 'rgba(85,255,0,0.4)';
        } elseif ($percentage >= 91) {
            return 'rgba(102,255,0,0.4)';
        } elseif ($percentage >= 90) {
            return 'rgba(119,255,0,0.4)';
        } elseif ($percentage >= 88) {
            return 'rgba(136,255,0,0.4)';
        } elseif ($percentage >= 87) {
            return 'rgba(153,255,0,0.4)';
        } elseif ($percentage >= 85) {
            return 'rgba(170,255,0,0.4)';
        } elseif ($percentage >= 84) {
            return 'rgba(187,255,0,0.4)';
        } elseif ($percentage >= 82) {
            return 'rgba(204,255,0,0.4)';
        } elseif ($percentage >= 81) {
            return 'rgba(221,255,0,0.4)';
        } elseif ($percentage >= 79) {
            return 'rgba(238,255,0,0.4)';
        } elseif ($percentage >= 78) {
            return 'rgba(255,255,0,0.4)';
        } //Yellow
        elseif ($percentage >= 75) {
            return 'rgba(255,238,0,0.4)';
        } elseif ($percentage >= 70) {
            return 'rgba(255,221,0,0.4)';
        } elseif ($percentage >= 65) {
            return 'rgba(255,204,0,0.4)';
        } elseif ($percentage >= 60) {
            return 'rgba(255,187,0,0.4)';
        } elseif ($percentage >= 55) {
            return 'rgba(255,170,0,0.4)';
        } elseif ($percentage >= 50) {
            return 'rgba(255,153,0,0.4)';
        } elseif ($percentage >= 45) {
            return 'rgba(255,136,0,0.4)';
        } elseif ($percentage >= 40) {
            return 'rgba(255,119,0,0.4)';
        } elseif ($percentage >= 35) {
            return 'rgba(255,102,0,0.4)';
        } elseif ($percentage >= 30) {
            return 'rgba(255,85,0,0.4)';
        } elseif ($percentage >= 25) {
            return 'rgba(255,68,0,0.4)';
        } elseif ($percentage >= 20) {
            return 'rgba(255,51,0,0.4)';
        } elseif ($percentage >= 15) {
            return 'rgba(255,34,0,0.4)';
        } elseif ($percentage >= 10) {
            return 'rgba(255,17,0,0.4)';
        } else {
            return 'rgba(255,0,0,0.4)';
        } //Red
    }

    protected function colorGenerator($percentage)
    {
        if ($percentage >= 100) {
            return '00FF00';
        } //Green
        elseif ($percentage >= 99) {
            return '11FF00';
        } elseif ($percentage >= 97) {
            return '22FF00';
        } elseif ($percentage >= 96) {
            return '33FF00';
        } elseif ($percentage >= 94) {
            return '44FF00';
        } elseif ($percentage >= 93) {
            return '55FF00';
        } elseif ($percentage >= 91) {
            return '66FF00';
        } elseif ($percentage >= 90) {
            return '77FF00';
        } elseif ($percentage >= 88) {
            return '88FF00';
        } elseif ($percentage >= 87) {
            return '99FF00';
        } elseif ($percentage >= 85) {
            return 'AAFF00';
        } elseif ($percentage >= 84) {
            return 'BBFF00';
        } elseif ($percentage >= 82) {
            return 'CCFF00';
        } elseif ($percentage >= 81) {
            return 'DDFF00';
        } elseif ($percentage >= 79) {
            return 'EEFF00';
        } elseif ($percentage >= 78) {
            return 'FFFF00';
        } //Yellow
        elseif ($percentage >= 75) {
            return 'FFEE00';
        } elseif ($percentage >= 70) {
            return 'FFDD00';
        } elseif ($percentage >= 65) {
            return 'FFCC00';
        } elseif ($percentage >= 60) {
            return 'FFBB00';
        } elseif ($percentage >= 55) {
            return 'FFAA00';
        } elseif ($percentage >= 50) {
            return 'FF9900';
        } elseif ($percentage >= 45) {
            return 'FF8800';
        } elseif ($percentage >= 40) {
            return 'FF7700';
        } elseif ($percentage >= 35) {
            return 'FF6600';
        } elseif ($percentage >= 30) {
            return 'FF5500';
        } elseif ($percentage >= 25) {
            return 'FF4400';
        } elseif ($percentage >= 20) {
            return 'FF3300';
        } elseif ($percentage >= 15) {
            return 'FF2200';
        } elseif ($percentage >= 10) {
            return 'FF1100';
        } else {
            return 'FF0000';
        } //Red
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{id}/", name="showPersoon")
     * @Method({"GET"})
     */
    public
    function showPersoon($id)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id, false, 'Index', true);
        $seizoen = $this->getSeizoen();
        foreach ($persoonItems->functies as $functie) {
            if ($functie->functie == 'Turnster') {
                /** @var Persoon $persoonObject */
                $persoonObject = $this->getPersoonObject($userObject, $id);
                if ($persoonObject->getLastUpdatedAtSeizoen() != $this->getSeizoen()) {
                    $persoonObject = $this->resetVoortgang($persoonObject);
                }
                $voortgang = array();
                $voortgang['Sprong'] = $persoonObject->getVoortgangSprong();
                $voortgang['Brug'] = $persoonObject->getVoortgangBrug();
                $voortgang['Balk'] = $persoonObject->getVoortgangBalk();
                $voortgang['Vloer'] = $persoonObject->getVoortgangVloer();
                $voortgang['Totaal'] = $persoonObject->getVoortgangTotaal();
                break;
            }
        }
        if (!isset($voortgang)) {
            $voortgang['Sprong'] = 100;
            $voortgang['Brug'] = 100;
            $voortgang['Balk'] = 100;
            $voortgang['Vloer'] = 100;
            $voortgang['Totaal'] = 100;
        }
        return $this->render('inloggen/selectieShowPersoon.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'voortgang' => $voortgang,
        ));
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/showDoelPrijzen/{persoonId}/{groepId}/", name="showDoelPrijzen")
     * @Method({"GET"})
     */
    public
    function showDoelPrijzen($persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId, false, 'Index');
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            /** @var Groepen $groep */
            $groep = $response['groep'];
            $people = $groep->getPeople();
            $countMedailles = 0;
            $countBekers = 0;
            $turnsters = array();
            $number = 0;
            foreach ($people as $person) {
                /** @var Persoon $person */
                /** @var Functie $functie */
                $functies = $person->getFunctie();
                foreach ($functies as $functie) {
                    if ($functie->getGroep() != $groep) continue;
                    if ($functie->getFunctie() == 'Turnster') {
                        $turnsters[$number] = new \stdClass();
                        $result = $this->getToestelPrijzen($person->getId());
                        $turnsters[$number]->doelenTachtigProcent = $result[0];
                        $turnsters[$number]->doelenNegentigProcent = $result[1];
                        $turnsters[$number]->doelenTachtigProcentGegeven = $result[5];
                        $turnsters[$number]->doelenNegentigProcentGegeven = $result[6];
                        $turnsters[$number]->doelenIds = $result[2];
                        $turnsters[$number]->naam = $person->getVoornaam() . ' ' . $person->getAchternaam();
                        $countMedailles += $result[3];
                        $countBekers += $result[4];
                        $number++;
                    }
                }
            }
            return $this->render('inloggen/selectieShowDoelPrijzen.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'persoon' => $persoon,
                'user' => $user,
                'persoonItems' => $persoonItems,
                'wedstrijdLinkItems' => $this->groepItems,
                'aantalMedailles' => $countMedailles,
                'aantalBekers' => $countBekers,
                'turnsters' => $turnsters,
                'groepId' => $groepId,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/showDoelPrijzen/{persoonId}/{groepId}/updateDoelPrijs/{doelId}/{percentage}/", name="updateDoelPrijzen")
     * @Method({"GET"})
     */
    public
    function updateDoelPrijzen($persoonId, $groepId, $doelId, $percentage)
    {
        $userObject = $this->getUser();
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT seizoensdoelen
                FROM AppBundle:SeizoensDoelen seizoensdoelen
                WHERE seizoensdoelen.id = :id')
                ->setParameter('id', $doelId);
            /** @var SeizoensDoelen $seizoensdoel */
            $seizoensdoel = $query->setMaxResults(1)->getOneOrNullResult();
            if ($percentage == 80) {
                $seizoensdoel->setTachtigProcent(true);
            }
            else {
                $seizoensdoel->setNegentigProcent(true);
            }
            $em->persist($seizoensdoel);
            $em->flush();
            return $this->redirectToRoute('showDoelPrijzen', array(
                'persoonId' => $persoonId,
                'groepId' => $groepId,
            ));
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/showDoelPrijzen/{persoonId}/{groepId}/undoDoelPrijs/{doelId}/{percentage}/", name="undoDoelPrijzen")
     * @Method({"GET"})
     */
    public
    function undoDoelPrijzen($persoonId, $groepId, $doelId, $percentage)
    {
        $userObject = $this->getUser();
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT seizoensdoelen
                FROM AppBundle:SeizoensDoelen seizoensdoelen
                WHERE seizoensdoelen.id = :id')
                ->setParameter('id', $doelId);
            /** @var SeizoensDoelen $seizoensdoel */
            $seizoensdoel = $query->setMaxResults(1)->getOneOrNullResult();
            if ($percentage == 80) {
                $seizoensdoel->setTachtigProcent(null);
            }
            else {
                $seizoensdoel->setNegentigProcent(null);
            }
            $em->persist($seizoensdoel);
            $em->flush();
            return $this->redirectToRoute('showDoelPrijzen', array(
                'persoonId' => $persoonId,
                'groepId' => $groepId,
            ));
        }
    }

    private function resetVoortgang(Persoon $persoonObject)
    {
        $persoonObject->setLastUpdatedAtSeizoen($this->getSeizoen());
        $persoonObject->setVoortgangSprong(null);
        $persoonObject->setVoortgangBrug(null);
        $persoonObject->setVoortgangBalk(null);
        $persoonObject->setVoortgangVloer(null);
        $persoonObject->setVoortgangTotaal(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($persoonObject);
        $em->flush();
        return $persoonObject;
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{persoonId}/Doelen/{toestel}/", name="showPersoonDoelenPerToestel")
     * @Method({"GET"})
     */
    public function showPersoonDoelenPerToestel($persoonId, $toestel)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        foreach ($persoonItems->functies as $functie) {
            if ($functie->functie == 'Turnster') {
                $response = $this->getDoelCijfersForTurnster($persoonId);
                $cijfers = $response[0];
                $kleuren = $response[1];
                $voortgang = $response[2];
                $doelen = $response[3];
                break;
            }
        }
        return $this->render('inloggen/selectieShowPersoonDoelenPerToestel.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'voortgang' => $voortgang,
            'doelen' => $doelen,
            'toestel' => $toestel,
            'kleuren' => $kleuren,
            'cijfers' => $cijfers,
        ));
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{persoonId}/Doelen/{toestel}/showDoel/{doelId}/", name="showPersoonOneDoelPerToestel")
     * @Method({"GET"})
     */
    public function showPersoonOneDoelPerToestel($persoonId, $toestel, $doelId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $subdoelIds = array();
        $collectedDoelen = array();
        $collectedDoelen[] = $doelId;
        $array = $this->getDoelOpbouw($doelId, $subdoelIds);
        $doelOpbouw = $array[0];
        $subdoelIds = $array[1];
        $result = $this->getSubdoelIds($subdoelIds, $collectedDoelen);
        $subdoelIds = $result[0];
        $extraDoelen = $result[1];
        $cijfers = array();
        $reveseExtraDoelen = array_reverse($extraDoelen);
        $repeat = true;
        while ($repeat) {
            $repeat = false;
            foreach ($reveseExtraDoelen as $id => $extraDoel) {
                if ($result = $this->getPercentages($extraDoel, $cijfers, $persoonId)) {
                    $cijfers = $result;
                    unset ($reveseExtraDoelen[$id]);
                    continue;
                }
                $repeat = true;
            }
        }
        $cijfers = $this->getPercentages($doelOpbouw, $cijfers, $persoonId);
        foreach ($cijfers as $id => $percentage) {
            $kleuren[$id] = $this->colorGenerator($percentage);
        }
        return $this->render('inloggen/showPersoonOneDoelPerToestel.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'doelOpbouw' => $doelOpbouw,
            'extraDoelen' => $extraDoelen,
            'cijfers' => $cijfers,
            'kleuren' => $kleuren,
            'toestel' => $toestel,
        ));
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{id}/afmelden/{groepId}/", name="Afmelding")
     * @Method({"GET", "POST"})
     */
    public
    function Afmelding($id, $groepId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id, true);
        $token = $this->getToken();
        if ($request->getMethod() == 'POST') {
            $postedToken = $request->request->get('token');
            if(!empty($postedToken)) {
                if ($this->isTokenValid($postedToken)) {
                    if(!empty($_POST['reden'])) {
                        /** @var Persoon $persoonObject */
                        $persoonObject = $this->getPersoonObject($userObject, $id);
                        $afmeldingsData = array();
                        $em = $this->getDoctrine()->getManager();
                        foreach ($_POST as $key => $value) {
                            if ($key != "reden" && $key != 'token') {
                                $query = $em->createQuery(
                                    'SELECT trainingsdata
                                FROM AppBundle:Trainingsdata trainingsdata
                                WHERE trainingsdata.id = :id')
                                    ->setParameter('id', $key);
                                /** @var Trainingsdata $trainingsdatum */
                                $trainingsdatum = $query->setMaxResults(1)->getOneOrNullResult();
                                $aanwezigheid = new Aanwezigheid();
                                $aanwezigheid->setAanwezig('A');
                                $aanwezigheid->setPersoon($persoonObject);
                                $aanwezigheid->setTrainingsdata($trainingsdatum);
                                $persoonObject->addAanwezigheid($aanwezigheid);
                                $lesdatum = $trainingsdatum->getLesdatum();
                                /** @var Trainingen $training */
                                $training = $trainingsdatum->getTrainingen();
                                $trainingsdag = $training->getDag();
                                $afmeldingsData[] = $trainingsdag . " " . $lesdatum->format('d-m-Y');;
                                $em->persist($persoonObject);
                                $em->flush();
                            } elseif ($key == "reden") {
                                $reden = $value;
                            }
                        }
                        $em = $this->getDoctrine()->getManager();
                        $query = $em->createQuery(
                            'SELECT functie
                        FROM AppBundle:Functie functie
                        WHERE functie.groep = :id
                        AND functie.functie = :functie')
                            ->setParameter('id', $groepId)
                            ->setParameter('functie', 'Trainer');
                        $trainers = $query->getResult();
                        foreach ($trainers as $trainer) {
                            $persoon = $trainer->getPersoon();
                            /** @var User $user */
                            $user = $persoon->getUser();
                            $message = \Swift_Message::newInstance()
                                ->setSubject('Afmelding ' . $persoonItems->voornaam . ' ' . $persoonItems->achternaam)
                                ->setFrom('afmeldingen@donargym.nl')
                                ->setTo($user->getUsername())
                                ->setBody(
                                    $this->renderView(
                                        'mails/afmelding.txt.twig',
                                        array(
                                            'voornaam' => $persoonItems->voornaam,
                                            'achternaam' => $persoonItems->achternaam,
                                            'afmeldingsData' => $afmeldingsData,
                                            'reden' => $reden,
                                        )
                                    ),
                                    'text/plain'
                                );
                            $this->get('mailer')->send($message);
                            $subject = 'Afmelding ' . $persoonItems->voornaam . ' ' . $persoonItems->achternaam;
                            $from = 'afmeldingen@donargym.nl';
                            $to = $user->getUsername();
                            $body = $message->getBody();
                            $afmeldingsObject = new Afmeldingen();
                            $afmeldingsObject->setBericht('FROM: ' . $from . ', TO: ' . $to . ', SUBJECT: ' . $subject . ', BERICHT: ' . $body);
                            $afmeldingsObject->setTurnster($persoonItems->voornaam . ' ' . $persoonItems->achternaam);
                            $afmeldingsObject->setDatum(new \DateTime('now'));
                            $em->persist($afmeldingsObject);
                            $em->flush();

                            if ($user->getEmail2()) {
                                $message = \Swift_Message::newInstance()
                                    ->setSubject('Afmelding ' . $persoonItems->voornaam . $persoonItems->achternaam)
                                    ->setFrom('afmeldingen@donargym.nl')
                                    ->setTo($user->getEmail2())
                                    ->setBody(
                                        $this->renderView(
                                            'mails/afmelding.txt.twig',
                                            array(
                                                'voornaam' => $persoonItems->voornaam,
                                                'achternaam' => $persoonItems->achternaam,
                                                'afmeldingsData' => $afmeldingsData,
                                                'reden' => $reden,
                                            )
                                        ),
                                        'text/plain'
                                    );
                                $this->get('mailer')->send($message);
                            }

                        }
                        return $this->redirectToRoute('showPersoon', array(
                            'id' => $id
                        ));
                    }
                    else {
                        $error = 'Vul alstublieft het veld "Opmerking" in';
                        $afmeldingsData = array();
                        foreach ($_POST as $key => $value) {
                            if ($key != "reden" && $key != 'token') {
                                $afmeldingsData[] = $key;
                            }
                        }
                        return $this->render('inloggen/selectieAfmelden.html.twig', array(
                            'calendarItems' => $this->calendarItems,
                            'header' => $this->header,
                            'persoon' => $persoon,
                            'user' => $user,
                            'persoonItems' => $persoonItems,
                            'wedstrijdLinkItems' => $this->groepItems,
                            'groepId' => $groepId,
                            'token' => $token,
                            'error' => $error,
                            'afmeldingsData' => $afmeldingsData,
                        ));
                    }
                }
                else {
                    return $this->redirectToRoute('showPersoon', array(
                        'id' => $id
                    ));
                }
            }
        }
        return $this->render('inloggen/selectieAfmelden.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'token' => $token,
        ));
    }

    protected function getAanwezigheid($userObject, $id, $groepId, $toekomst)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                $functies = $persoon->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Functie $functie */
                    if ($functie->getFunctie() == 'Trainer' || $functie->getFunctie() == 'Assistent-Trainer') {
                        /** @var Groepen $groep */
                        $groep = $functie->getGroep();
                        if ($groep->getId() == $groepId) {
                            $aanwezigheid = new \stdClass();
                            $trainingen = $groep->getTrainingen();
                            $aanwezigheid->trainingen = array();
                            for ($i = 0; $i < count($trainingen); $i++) {
                                $aanwezigheid->trainingen[$i] = new \stdClass();
                                $aanwezigheid->trainingen[$i]->dag = $trainingen[$i]->getDag();
                                $aanwezigheid->trainingen[$i]->tijdVan = $trainingen[$i]->getTijdvan();
                                $aanwezigheid->trainingen[$i]->tijdTot = $trainingen[$i]->getTijdtot();
                                $aanwezigheid->trainingen[$i]->trainingsdata = array();
                                $aanwezigheid->trainingen[$i]->turnsters = array();
                                $aanwezigheid->trainingen[$i]->trainers = array();
                                $aanwezigheid->trainingen[$i]->assistenten = array();
                                $trainingsdata = $trainingen[$i]->getTrainingsdata();
                                $personenPerTraining = $trainingen[$i]->getPersoon();
                                for ($j = 0; $j < count($personenPerTraining); $j++) {
                                    /** @var Functie $functiePerPersoon */
                                    $functiesPerPersoon = $personenPerTraining[$j]->getFunctie();
                                    foreach ($functiesPerPersoon as $functiePerPersoon) {
                                        $groepPerPersoon = $functiePerPersoon->getGroep();
                                        if ($groepPerPersoon->getId() == $groepId) {
                                            $aantalTrainingen = 0;
                                            $aantalAanwezig = 0;
                                            $aanwezighedenPerPersoon = $personenPerTraining[$j]->getAanwezigheid();
                                            for ($jj = (count($aanwezighedenPerPersoon) - 1); $jj >= 0; $jj--) {
                                                /** @var Aanwezigheid $aanwezigheidPerPersoon */
                                                $aanwezigTrainingsdata = $aanwezighedenPerPersoon[$jj]->getTrainingsdata();
                                                $lesdatum = $aanwezigTrainingsdata->getLesdatum();
                                                $timestamp = $lesdatum->getTimestamp();
                                                if ($aanwezigTrainingsdata->getTrainingen() == $trainingen[$i] && $timestamp < time()) {
                                                    $check = false;
                                                    if (date('m', time()) < '08') {
                                                        if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('m') < '08') ||
                                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('m') >= '08')
                                                        ) {
                                                            $check = true;
                                                        } else {
                                                            break;
                                                        }
                                                    } else {
                                                        if ($lesdatum->format('Y') == date('Y', time())) {
                                                            if ($lesdatum->format('m') < '08') {
                                                                break;
                                                            } else {
                                                                $check = true;
                                                            }
                                                        }
                                                    }
                                                    if ($check) {
                                                        $aantalTrainingen++;
                                                        if ($aanwezighedenPerPersoon[$jj]->getAanwezig() == 'X') {
                                                            $aantalAanwezig++;
                                                        }
                                                    }
                                                }
                                            }
                                            if ($aantalTrainingen == 0) {
                                                $percentageAanwezig = 100;
                                            } else {
                                                $percentageAanwezig = (100 * ($aantalAanwezig / $aantalTrainingen));
                                            }
                                            $percentageKleur = $this->colorGenerator($percentageAanwezig);
                                            if ($functiePerPersoon->getFunctie() == 'Trainer') {
                                                $aanwezigheid->trainingen[$i]->trainers[$j] = new \stdClass();
                                                $aanwezigheid->trainingen[$i]->trainers[$j]->voornaam = $personenPerTraining[$j]->getVoornaam();
                                                $aanwezigheid->trainingen[$i]->trainers[$j]->achternaam = $personenPerTraining[$j]->getAchternaam();
                                                $aanwezigheid->trainingen[$i]->trainers[$j]->id = $personenPerTraining[$j]->getId();
                                                $aanwezigheid->trainingen[$i]->trainers[$j]->percentageAanwezig = $percentageAanwezig;
                                                $aanwezigheid->trainingen[$i]->trainers[$j]->percentageKleur = $percentageKleur;
                                            } elseif ($functiePerPersoon->getFunctie() == 'Assistent-Trainer') {
                                                $aanwezigheid->trainingen[$i]->assistenten[$j] = new \stdClass();
                                                $aanwezigheid->trainingen[$i]->assistenten[$j]->voornaam = $personenPerTraining[$j]->getVoornaam();
                                                $aanwezigheid->trainingen[$i]->assistenten[$j]->achternaam = $personenPerTraining[$j]->getAchternaam();
                                                $aanwezigheid->trainingen[$i]->assistenten[$j]->id = $personenPerTraining[$j]->getId();
                                                $aanwezigheid->trainingen[$i]->assistenten[$j]->percentageAanwezig = $percentageAanwezig;
                                                $aanwezigheid->trainingen[$i]->assistenten[$j]->percentageKleur = $percentageKleur;
                                            } elseif ($functiePerPersoon->getFunctie() == 'Turnster') {
                                                $aanwezigheid->trainingen[$i]->turnsters[$j] = new \stdClass();
                                                $aanwezigheid->trainingen[$i]->turnsters[$j]->voornaam = $personenPerTraining[$j]->getVoornaam();
                                                $aanwezigheid->trainingen[$i]->turnsters[$j]->achternaam = $personenPerTraining[$j]->getAchternaam();
                                                $aanwezigheid->trainingen[$i]->turnsters[$j]->id = $personenPerTraining[$j]->getId();
                                                $aanwezigheid->trainingen[$i]->turnsters[$j]->percentageAanwezig = $percentageAanwezig;
                                                $aanwezigheid->trainingen[$i]->turnsters[$j]->percentageKleur = $percentageKleur;
                                            }
                                        }
                                    }
                                }
                                $counter = 0;
                                for ($j = (count($trainingsdata) - 4); $j >= 0; $j--) {
                                    if ($toekomst) {
                                        $lesdatum = $trainingsdata[$j]->getLesdatum();
                                        $timestamp = $lesdatum->getTimestamp();
                                        $timestampPlusDag = ((int)$timestamp + 86400);
                                        if ($timestampPlusDag > time()) {
                                            $aanwezigheid->trainingen[$i]->trainingsdata[$j] = new \stdClass();
                                            $aanwezigheid->trainingen[$i]->trainingsdata[$j]->lesdatum = $lesdatum->format('d-m-Y');
                                            $aanwezigheid->trainingen[$i]->trainingsdata[$j]->aanwezigheid = array();
                                            $aanwezigheidPersonen = $trainingsdata[$j]->getAanwezigheid();
                                            /** @var Aanwezigheid $aanwezigheidPersoon */
                                            foreach ($aanwezigheidPersonen as $aanwezigheidPersoon) {
                                                $persoonsObject = $aanwezigheidPersoon->getPersoon();
                                                $aanwezigheid->trainingen[$i]->trainingsdata[$j]->aanwezigheid[$persoonsObject->getId()] = $aanwezigheidPersoon->getAanwezig();
                                            }
                                            $counter++;
                                            if ($counter == 7) {
                                                $j = 0;
                                                $counter++;
                                            }
                                        }
                                    } else {
                                        $lesdatum = $trainingsdata[$j]->getLesdatum();
                                        $timestamp = $lesdatum->getTimestamp();
                                        if ($timestamp < time()) {
                                            $aanwezigheid->trainingen[$i]->trainingsdata[$j] = new \stdClass();
                                            $aanwezigheid->trainingen[$i]->trainingsdata[$j]->lesdatum = $lesdatum->format('d-m-Y');
                                            $aanwezigheid->trainingen[$i]->trainingsdata[$j]->aanwezigheid = array();
                                            $aanwezigheidPersonen = $trainingsdata[$j]->getAanwezigheid();
                                            /** @var Aanwezigheid $aanwezigheidPersoon */
                                            foreach ($aanwezigheidPersonen as $aanwezigheidPersoon) {
                                                $persoonsObject = $aanwezigheidPersoon->getPersoon();
                                                $aanwezigheid->trainingen[$i]->trainingsdata[$j]->aanwezigheid[$persoonsObject->getId()] = $aanwezigheidPersoon->getAanwezig();
                                            }
                                            $counter++;
                                            if ($counter == 7) {
                                                $j = 0;
                                                $counter++;
                                            }
                                        }
                                    }
                                }
                                if ($toekomst) {
                                    if ($counter < 7) {
                                        if (count($trainingsdata) == 0) {
                                            for ($try = 0; $try < 7; $try++) {
                                                $dag = $this->dayToDutch((time() + ($try * 86400)));
                                                if ($dag == $aanwezigheid->trainingen[$i]->dag) {
                                                    $lesdatum = date('Y-m-d', (time() + ($try * 86400) - 604800));
                                                    $try = 7;
                                                }
                                            }
                                        } else {
                                            $j = (count($trainingsdata) - 1);
                                            $lesdatum = $trainingsdata[$j]->getLesdatum();
                                            $lesdatum = $lesdatum->format('Y-m-d');
                                        }
                                        $week = (604800 + 12 * 3600);
                                        for ($counter; $counter < 7; $counter++) {
                                            $lesdatum = date('Y-m-d', (strtotime($lesdatum) + $week));
                                            $lesdatumForDb = \DateTime::createFromFormat('Y-m-d', $lesdatum);
                                            $newLesdatum = new Trainingsdata();
                                            $newLesdatum->setLesdatum($lesdatumForDb);
                                            $newLesdatum->setTrainingen($trainingen[$i]);
                                            $em = $this->getDoctrine()->getManager();
                                            $em->persist($newLesdatum);
                                            $em->flush();
                                        }
                                    }
                                }
                                $aanwezigheid->trainingen[$i]->trainingsdata = array_reverse($aanwezigheid->trainingen[$i]->trainingsdata);
                            }
                            return $aanwezigheid;
                        }
                    }
                }
            }
        }
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{id}/viewAfmeldingen/{groepId}/", name="viewAfmeldingen")
     * @Method({"GET"})
     */
    public
    function viewAfmeldingen($id, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        $aanwezigheid = $this->getAanwezigheid($userObject, $id, $groepId, true);
        return $this->render('inloggen/selectieViewAfmeldingen.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'aanwezigheid' => $aanwezigheid,
        ));
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{id}/viewAanwezigheid/{groepId}/", name="viewAanwezigheid")
     * @Method({"GET"})
     */
    public
    function viewAanwezigheid($id, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        $aanwezigheid = $this->getAanwezigheid($userObject, $id, $groepId, false);
        return $this->render('inloggen/selectieViewAanwezigheid.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'aanwezigheid' => $aanwezigheid,
        ));
    }

    protected function getTrainingsdataVoorKruisjeslijst($userObject, $id, $groepId)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                $functies = $persoon->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Functie $functie */
                    if ($functie->getFunctie() == 'Trainer') {
                        /** @var Groepen $groep */
                        $groep = $functie->getGroep();
                        if ($groep->getId() == $groepId) {
                            $trainingen = $groep->getTrainingen();
                            $trainingsdataVoorKruisjeslijst = new \stdClass();
                            $trainingsdataVoorKruisjeslijst->trainingen = array();
                            for ($j = 0; $j < count($trainingen); $j++) {
                                $trainingsdataVoorKruisjeslijst->trainingen[$j] = new \stdClass();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->dag = $trainingen[$j]->getDag();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdvan();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdtot();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata = array();
                                $trainingsdata = $trainingen[$j]->getTrainingsdata();
                                for ($i = (count($trainingsdata) - 1); $i >= 0; $i--) {
                                    $lesdatum = $trainingsdata[$i]->getLesdatum();
                                    $timestamp = $lesdatum->getTimestamp();
                                    if (($timestamp) > (time() - 604800)) {
                                        $trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata[$i] = new \stdClass();
                                        $trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata[$i]->id = $trainingsdata[$i]->getId();
                                        $trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata[$i]->datum = $lesdatum->format('d-m-Y');
                                    }
                                }
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata = array_reverse($trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata);
                            }
                            return $trainingsdataVoorKruisjeslijst;
                        }
                    }
                }
            }
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{id}/kruisjeslijst/{groepId}/", name="kruisjeslijst")
     * @Method({"GET"})
     */
    public
    function kruisjeslijst($id, $groepId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        $trainingsdata = $this->getTrainingsdataVoorKruisjeslijst($userObject, $id, $groepId);
        return $this->render('inloggen/selectieKruisjeslijst.html.twig', array(
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

    protected function getTrainingsdatumDetails($userObject, $id, $groepId, $trainingsdatumId)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                $functies = $persoon->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Functie $functie */
                    if ($functie->getFunctie() == 'Trainer') {
                        /** @var Groepen $groep */
                        $groep = $functie->getGroep();
                        if ($groep->getId() == $groepId) {
                            $trainingen = $groep->getTrainingen();
                            for ($j = 0; $j < count($trainingen); $j++) {
                                $trainingsdata = $trainingen[$j]->getTrainingsdata();
                                for ($i = (count($trainingsdata) - 1); $i >= 0; $i--) {
                                    if ($trainingsdata[$i]->getId() == $trainingsdatumId) {
                                        return $trainingsdata[$i];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{id}/kruisjeslijst/{groepId}/removeTraining/{trainingsdatumId}/", name="removeTrainingsdatum")
     * @Method({"GET", "POST"})
     */
    public
    function removeTrainingsdatum($id, $groepId, $trainingsdatumId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        /** @var Trainingsdata $trainingsdataObject */
        $trainingsdataObject = $this->getTrainingsdatumDetails($userObject, $id, $groepId, $trainingsdatumId);
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trainingsdataObject);
            $em->flush();
            return $this->redirectToRoute('kruisjeslijst', array(
                'id' => $id,
                'groepId' => $groepId,
            ));
        }
        $trainingsdata = new \stdClass();
        $trainingsdata->id = $trainingsdataObject->getId();
        $lesdatum = $trainingsdataObject->getLesdatum();
        $trainingsdata->lesdatum = $lesdatum->format('d-m-Y');
        return $this->render('inloggen/removeTrainingsdatum.html.twig', array(
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

    protected function getPersonenVoorTrainingsdatum($trainingsdataObject, $groepId)
    {
        /** @var Trainingsdata $trainingsdataObject */
        /** @var Trainingen $training */
        $training = $trainingsdataObject->getTrainingen();
        $aanwezigheidPersonen = new \stdClass();
        $personen = $training->getPersoon();
        $aanwezigheidPersonen->trainers = array();
        $aanwezigheidPersonen->assistenten = array();
        $aanwezigheidPersonen->turnsters = array();
        for ($i = 0; $i < count($personen); $i++) {
            $functies = $personen[$i]->getFunctie();
            foreach ($functies as $functie) {
                $groep = $functie->getGroep();
                if ($groep->getId() == $groepId) {
                    if ($functie->getFunctie() == 'Trainer') {
                        $aanwezigheidPersonen->trainers[$i] = new \stdClass();
                        $aanwezigheidPersonen->trainers[$i]->voornaam = $personen[$i]->getVoornaam();
                        $aanwezigheidPersonen->trainers[$i]->achternaam = $personen[$i]->getAchternaam();
                        $aanwezigheidPersonen->trainers[$i]->id = $personen[$i]->getId();
                    } elseif ($functie->getFunctie() == 'Assistent-Trainer') {
                        $aanwezigheidPersonen->assistenten[$i] = new \stdClass();
                        $aanwezigheidPersonen->assistenten[$i]->voornaam = $personen[$i]->getVoornaam();
                        $aanwezigheidPersonen->assistenten[$i]->achternaam = $personen[$i]->getAchternaam();
                        $aanwezigheidPersonen->assistenten[$i]->id = $personen[$i]->getId();
                    } elseif ($functie->getFunctie() == 'Turnster') {
                        $aanwezigheidPersonen->turnsters[$i] = new \stdClass();
                        $aanwezigheidPersonen->turnsters[$i]->voornaam = $personen[$i]->getVoornaam();
                        $aanwezigheidPersonen->turnsters[$i]->achternaam = $personen[$i]->getAchternaam();
                        $aanwezigheidPersonen->turnsters[$i]->id = $personen[$i]->getId();
                    }
                }
            }
        }
        $aanwezigheid = $trainingsdataObject->getAanwezigheid();
        $aanwezigheidPersonen->aanwezigheid = array();
        for ($i = 0; $i < count($aanwezigheid); $i++) {
            $aanwezigePersoon = $aanwezigheid[$i]->getPersoon();
            $aanwezigheidPersonen->aanwezigheid[$aanwezigePersoon->getId()] = $aanwezigheid[$i]->getAanwezig();;
        }
        return $aanwezigheidPersonen;
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{id}/kruisjeslijst/{groepId}/invullen/{trainingsdatumId}/", name="kruisjeslijstInvullen")
     * @Method({"GET", "POST"})
     */
    public
    function kruisjeslijstInvullen($id, $groepId, $trainingsdatumId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        /** @var Trainingsdata $trainingsdataObject */
        $trainingsdataObject = $this->getTrainingsdatumDetails($userObject, $id, $groepId, $trainingsdatumId);
        $personenAanwezigheid = $this->getPersonenVoorTrainingsdatum($trainingsdataObject, $groepId);
        if ($request->getMethod() == 'POST') {
            /** @var Persoon $persoonObject */
            $afgemeldMaarAanwezig = array();
            $aanwezigeIds = array();
            foreach ($_POST as $key => $value) {
                if (preg_match("/^afgemeld/", $key)) {
                    $afgemeldMaarAanwezigid = explode("_", $key);
                    $afgemeldMaarAanwezig[] = (int)$afgemeldMaarAanwezigid[1];
                } else {
                    $aanwezigeIds[] = $key;
                }
            }
            $aanwezigheiden = $trainingsdataObject->getAanwezigheid();
            $afgemeldeIds = array();
            /** @var Aanwezigheid $aanwezigheid */
            foreach ($aanwezigheiden as $aanwezigheid) {
                $persoonAanwezigheid = $aanwezigheid->getPersoon();
                $afgemeldeIds[] = $persoonAanwezigheid->getId();
            }
            $training = $trainingsdataObject->getTrainingen();
            $personenVoorDezeTraining = $training->getPersoon();
            $em = $this->getDoctrine()->getManager();
            foreach ($personenVoorDezeTraining as $persoonVoorDezeTraining) {
                if (in_array($persoonVoorDezeTraining->getId(), $afgemeldMaarAanwezig)) {
                    $persoonsId = $persoonVoorDezeTraining->getId();
                    $query = $em->createQuery(
                        'SELECT aanwezigheid
                        FROM AppBundle:Aanwezigheid aanwezigheid
                        WHERE aanwezigheid.persoon = :persoon
						AND aanwezigheid.trainingsdata = :training')
                        ->setParameter('persoon', $persoonsId)
                        ->setParameter('training', $trainingsdatumId);
                    /** @var Aanwezigheid $aanwezig */
                    $aanwezig = $query->setMaxResults(1)->getOneOrNullResult();
                    $aanwezig->setAanwezig('X');
                    $em->flush();
                } elseif (in_array($persoonVoorDezeTraining->getId(), $aanwezigeIds)) {
                    $aanwezig = new Aanwezigheid();
                    $aanwezig->setPersoon($persoonVoorDezeTraining);
                    $aanwezig->setTrainingsdata($trainingsdataObject);
                    $aanwezig->setAanwezig('X');
                    $em->persist($aanwezig);
                    $em->flush();
                } elseif (in_array($persoonVoorDezeTraining->getId(), $afgemeldeIds)) {
                } else {
                    $aanwezig = new Aanwezigheid();
                    $aanwezig->setPersoon($persoonVoorDezeTraining);
                    $aanwezig->setTrainingsdata($trainingsdataObject);
                    $aanwezig->setAanwezig('-');
                    $em->persist($aanwezig);
                    $em->flush();
                }
            }
            return $this->redirectToRoute('showPersoon', array(
                'id' => $id
            ));
        }
        $trainingsdata = new \stdClass();
        $trainingsdata->id = $trainingsdataObject->getId();
        $lesdatum = $trainingsdataObject->getLesdatum();
        $trainingsdata->lesdatum = $lesdatum->format('d-m-Y');
        $trainingsdata->dag = $this->dayToDutch($lesdatum->getTimestamp());
        return $this->render('inloggen/kruisjeslijstInvullen.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
            'trainingsdata' => $trainingsdata,
            'personenAanwezigheid' => $personenAanwezigheid,
        ));
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{id}/adreslijst/{groepId}/", name="viewAdreslijst")
     * @Method({"GET", "POST"})
     */
    public
    function viewAdreslijst($id, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id, true);
        return $this->render('inloggen/selectieAdreslijst.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
        ));
    }


    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{id}/stukje/", name="addSelectieStukjePage")
     * @Method({"GET", "POST"})
     */
    public function addStukje($id, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id, true);
        if ($request->getMethod() == 'POST') {
            /** @var Persoon $persoonObject */
            $persoonObject = $this->getPersoonObject($userObject, $id);
            $stukje = $persoonObject->getStukje();
            $stukje->setLeren($request->request->get('leren'));
            $stukje->setOmdattoestelleuk($request->request->get('omdattoestelleuk'));
            $stukje->setElement($request->request->get('element'));
            $stukje->setOverig($request->request->get('overig'));
            $stukje->setToestelleuk($request->request->get('toestelleuk'));
            $stukje->setVoorbeeld($request->request->get('voorbeeld'));
            $stukje->setWedstrijd($request->request->get('wedstrijd'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($stukje);
            $em->flush();
            return $this->redirectToRoute('showPersoon', array(
                'id' => $id
            ));
        }
        return $this->render('inloggen/selectieAddStukje.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{id}/add/{groepsId}", name="addSelectieTurnsterPage")
     * @Method({"GET", "POST"})
     */
    public
    function addSelectieTurnsterPageAction(Request $request, $id, $groepsId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT groepen
                FROM AppBundle:Groepen groepen
                WHERE groepen.id = :id')
            ->setParameter('id', $groepsId);
        $groepen = $query->getResult();
        $groepenItems = array();
        for ($i = 0; $i < count($groepen); $i++) {
            $groepenItems[$i] = new \stdClass();
            $groepenItems[$i]->id = $groepen[$i]->getId();
            $groepenItems[$i]->naam = $groepen[$i]->getName();
            $groepenItems[$i]->trainingen = array();
            $query = $em->createQuery(
                'SELECT trainingen
                FROM AppBundle:Trainingen trainingen
                WHERE trainingen.groep = :id')
                ->setParameter('id', $groepen[$i]->getId());
            $trainingen = $query->getResult();
            for ($j = 0; $j < count($trainingen); $j++) {
                $groepenItems[$i]->trainingen[$j] = new \stdClass();
                $groepenItems[$i]->trainingen[$j]->dag = $trainingen[$j]->getDag();
                $groepenItems[$i]->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdVan();
                $groepenItems[$i]->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdTot();
                $groepenItems[$i]->trainingen[$j]->id = $trainingen[$j]->getId();
            }
        }
        if ($request->getMethod() == 'POST') {
            $role = 'ROLE_TURNSTER';
            $query = $em->createQuery(
                'SELECT user
                FROM AppBundle:User user
                WHERE user.username = :email
                OR user.email2 = :email')
                ->setParameter('email', $this->get('request')->request->get('username'));
            $user = $query->setMaxResults(1)->getOneOrNullResult();
            if (count($user) == 0) {
                $query = $em->createQuery(
                    'SELECT user
                FROM AppBundle:User user
                WHERE user.username = :email
                OR user.email2 = :email')
                    ->setParameter('email', $this->get('request')->request->get('email2'));
                $user = $query->setMaxResults(1)->getOneOrNullResult();
            }


            if (count($user) > 0) {
                $role = $user->getRole();
                $newuser = false;
            } else {
                $user = new \AppBundle\Entity\User();
                $newuser = true;
            }
            $persoon = new Persoon();

            $k = 0;
            $postGroepen = array();
            foreach ($groepen as $groep) {
                if ($this->get('request')->request->get('groep_' . $groep->getId()) == 'Turnster') {
                    if ($this->get('request')->request->get('groep_' . $groep->getId()) == 'Trainer') {
                        $role = 'ROLE_TRAINER';
                    } elseif ($this->get('request')->request->get('groep_' . $groep->getId()) == 'Assistent-Trainer' && $role != 'ROLE_TRAINER') {
                        $role = 'ROLE_ASSISTENT';
                    }
                    $query = $em->createQuery(
                        'SELECT groepen
                        FROM AppBundle:Groepen groepen
                        WHERE groepen.id = :id')
                        ->setParameter('id', $groep->getId());
                    $result = $query->setMaxResults(1)->getOneOrNullResult();
                    $postGroepen[$k] = $result;
                    $functie = new Functie();
                    $functie->setFunctie($this->get('request')->request->get('groep_' . $groep->getId()));
                    $postGroepen[$k]->addFunctie($functie);
                    $persoon->addFunctie($functie);
                    $query = $em->createQuery(
                        'SELECT trainingen
                        FROM AppBundle:Trainingen trainingen
                        WHERE trainingen.groep = :id')
                        ->setParameter('id', $groep->getId());
                    $trainingen = $query->getResult();
                    foreach ($trainingen as $training) {
                        if ($this->get('request')->request->get('trainingen_' . $training->getId()) == 'on') {
                            $query = $em->createQuery(
                                'SELECT trainingen
                                FROM AppBundle:Trainingen trainingen
                                WHERE trainingen.id = :id')
                                ->setParameter('id', $training->getId());
                            $result = $query->setMaxResults(1)->getOneOrNullResult();
                            $persoon->addTrainingen($result);
                        }
                    }
                }
            }
            $persoon->setVoornaam($this->get('request')->request->get('voornaam'));
            $persoon->setAchternaam($this->get('request')->request->get('achternaam'));
            $persoon->setGeboortedatum($this->get('request')->request->get('geboortedatum'));
            $stukje = new Stukje();
            $persoon->setStukje($stukje);
            $user->setRole($role);
            $user->setUsername($this->get('request')->request->get('username'));
            if ($this->get('request')->request->get('email2')) {
                $user->setEmail2($this->get('request')->request->get('email2'));
            }
            $user->setStraatnr($this->get('request')->request->get('straatnr'));
            $user->setPostcode($this->get('request')->request->get('postcode'));
            $user->setPlaats($this->get('request')->request->get('plaats'));
            $user->setTel1($this->get('request')->request->get('tel1'));
            if ($this->get('request')->request->get('tel2')) {
                $user->setTel2($this->get('request')->request->get('tel2'));
            }
            if ($this->get('request')->request->get('tel3')) {
                $user->setTel3($this->get('request')->request->get('tel3'));
            }

            $persoon->setUser($user);

            if ($newuser) {
                $password = $this->generatePassword();
                $encoder = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em->persist($user);
            } else {
                $password = 'over een wachtwoord beschik je als het goed is al';
            }
            $this->addSubDoelenAanPersoon($persoon);
            $user->addPersoon($persoon);
            $em->persist($persoon);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Inloggegevens website Donar')
                ->setFrom('webmaster@donargym.nl')
                ->setTo($user->getUsername())
                ->setBody(
                    $this->renderView(
                        'mails/new_user.txt.twig',
                        array(
                            'voornaam' => $persoon->getVoornaam(),
                            'email1' => $user->getUsername(),
                            'email2' => $user->getEmail2(),
                            'password' => $password
                        )
                    ),
                    'text/plain'
                );
            $this->get('mailer')->send($message);

            if ($user->getEmail2()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Inloggegevens website Donar')
                    ->setFrom('webmaster@donargym.nl')
                    ->setTo($user->getEmail2())
                    ->setBody(
                        $this->renderView(
                            'mails/new_user.txt.twig',
                            array(
                                'voornaam' => $persoon->getVoornaam(),
                                'email1' => $user->getUsername(),
                                'email2' => $user->getEmail2(),
                                'password' => $password
                            )
                        ),
                        'text/plain'
                    );
                $this->get('mailer')->send($message);
            }
            return $this->redirectToRoute('showPersoon', array(
                'id' => $id
            ));
        }
        return $this->render('inloggen/selectieAddTurnster.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'groepen' => $groepenItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{trainerId}/remove/{turnsterId}/{groepId}", name="removeSelectieTurnsterPage")
     * @Method({"GET", "POST"})
     */
    public
    function removeSelectieTurnsterPage($trainerId, $turnsterId, $groepId, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData('wedstrijdturnen');
            $userObject = $this->getUser();
            $user = $this->getBasisUserGegevens($userObject);
            $persoon = $this->getBasisPersoonsGegevens($userObject);
            $persoonItems = $this->getOnePersoon($userObject, $trainerId);
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
                ->setParameter('id', $turnsterId);
            $turnster = $query->setMaxResults(1)->getOneOrNullResult();
            if (count($turnster) > 0) {
                return $this->render('inloggen/selectieRemoveTurnster.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'voornaam' => $turnster->getVoornaam(),
                    'achternaam' => $turnster->getAchternaam(),
                    'id' => $turnster->getId(),
                    'wedstrijdLinkItems' => $this->groepItems,
                    'persoon' => $persoon,
                    'user' => $user,
                    'persoonItems' => $persoonItems,
                ));
            } else {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
        } elseif ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
                ->setParameter('id', $turnsterId);
            $persoon = $query->setMaxResults(1)->getOneOrNullResult();
            $functies = $persoon->getFunctie();
            $user = $persoon->getUser();
            $personen = $user->getPersoon();
            if (count($functies) == 1) {
                $em->remove($persoon);
                $em->flush();
            } else {
                foreach ($functies as $functie) {
                    $groep = $functie->getGroep();
                    if ($groep->getId() == $groepId && $functie->getFunctie() == 'Turnster') {
                        $em->remove($functie);
                        $em->flush();
                    }
                }
            }
            $role = 'ROLE_TURNSTER';
            if (count($personen) == 0) {
                $em->remove($user);
                $em->flush();
            } else {
                foreach ($personen as $persoonItem) {
                    $functie = $persoonItem->getFunctie();
                    foreach ($functie as $functieItem) {
                        if ($functieItem->getFunctie() == 'Trainer') {
                            $role = 'ROLE_TRAINER';
                        } elseif ($functieItem->getFunctie() == 'Assistent-Trainer' && $role == 'ROLE_TURNSTER') {
                            $role = 'ROLE_ASSISTENT';
                        }
                    }
                }
                $user->setRole($role);
                $em->flush();
            }
            return $this->redirectToRoute('showPersoon', array(
                'id' => $trainerId
            ));
        } else {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Template()
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{persoonId}/addFoto/", name="addSelectieFotoPage")
     * @Method({"GET", "POST"})
     */
    public
    function addSelectieFotoPageAction(Request $request, $persoonId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $foto = new SelectieFoto();
        $form = $this->createFormBuilder($foto)
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $personen = $userObject->getPersoon();
            foreach ($personen as $persoonObject) {
                /** @var Persoon $persoonObject */
                if ($persoonObject->getId() == $persoonId) {
                    $persoonObject->setFoto($foto);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($persoonObject);
                    $em->flush();
                    $this->get('helper.imageresizer')->resizeImage($foto->getAbsolutePath(), $foto->getUploadRootDir() . "/", null, $width = 200);
                    return $this->redirectToRoute('showPersoon', array(
                        'id' => $persoonId
                    ));
                }
            }
        } else {
            return $this->render('inloggen/selectieAddFoto.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'wedstrijdLinkItems' => $this->groepItems,
                'persoon' => $persoon,
                'user' => $user,
                'persoonItems' => $persoonItems,
            ));
        }
    }

    protected function checkGroupAuthorization($userObject, $id, $groepId, array $roles)
    {
        $response = array();
        $authorized = false;
        $groep = null;
        $functie = null;
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                $functies = $persoon->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Functie $functie */
                    if (in_array($functie->getFunctie(), $roles)) {
                        /** @var Groepen $groep */
                        $groep = $functie->getGroep();
                        if ($groep->getId() == $groepId) {
                            $authorized = true;
                            $response['authorized'] = $authorized;
                            $response['groep'] = $groep;
                            $response['functie'] = $functie->getFunctie();
                            break;
                        }
                    }
                }
            }
        }
        return $response;
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/wedstrijduitslagen/{groepId}/", name="viewWedstrijduitslagen")
     * @Method({"GET"})
     */
    public
    function viewSelectieWedstrijduitslagen(Request $request, $persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $wedstrijduitslagen = array();
            $groepObject = $response['groep'];
            $functie = $response['functie'];
            $uitslagen = $groepObject->getWedstrijduitslagen();
            for ($counter = (count($uitslagen) - 1); $counter >= 0; $counter--) {
                if ($uitslagen[$counter]->getDatum()->format('m') > 7) {
                    $wedstrijduitslagen[$uitslagen[$counter]->getDatum()->format('Y')][] = $uitslagen[$counter]->getAll();
                } else {
                    $wedstrijduitslagen[($uitslagen[$counter]->getDatum()->format('Y') - 1)][] = $uitslagen[$counter]->getAll();
                }
            }
        }
        return $this->render('inloggen/selectieViewWedstrijduitslagen.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijduitslagen' => $wedstrijduitslagen,
            'functie' => $functie,
            'groepId' => $groepId,
        ));
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{persoonId}/addWedstrijduitslagen/{groepId}/", name="addWedstrijduitslagen")
     * @Method({"GET", "POST"})
     */
    public
    function addSelectieWedstrijduitslagen(Request $request, $persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $groepObject = $response['groep'];
            $functie = $response['functie'];
            $wedstrijduitslag = new Wedstrijduitslagen();
            $form = $this->createFormBuilder($wedstrijduitslag)
                ->add('naam')
                ->add('datum', 'date', array(
                    'widget' => 'single_text',
                ))
                ->add('file')
                ->add('uploadBestand', 'submit')
                ->getForm();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $wedstrijduitslag->setGroep($groepObject);
                $em->persist($wedstrijduitslag);
                $em->flush();
                return $this->redirectToRoute('viewWedstrijduitslagen', array(
                    'persoonId' => $persoonId,
                    'groepId' => $groepId,
                ));
            }


            return $this->render('inloggen/selectieAddWedstrijduitslagen.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
                'persoon' => $persoon,
                'user' => $user,
                'persoonItems' => $persoonItems,
                'functie' => $functie,
                'groepId' => $groepId,
                'form' => $form->createView(),
            ));
        }
        return $this->render('error/NotAuthorized.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{persoonId}/removeWedstrijduitslagen/{groepId}/{wedstrijduitslagId}/", name="removeWedstrijduitslagen")
     * @Method({"GET", "POST"})
     */
    public function removeSelectieWedstrijduitslagen(Request $request, $persoonId, $groepId, $wedstrijduitslagId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT wedstrijduitslagen
            FROM AppBundle:Wedstrijduitslagen wedstrijduitslagen
            WHERE wedstrijduitslagen.id = :id')
                ->setParameter('id', $wedstrijduitslagId);
            $wedstrijduitslag = $query->setMaxResults(1)->getOneOrNullResult();
            $uitslag = new \stdClass();
            $uitslag->naam = $wedstrijduitslag->getNaam();
            $uitslag->id = $wedstrijduitslag->getId();
        }
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($wedstrijduitslag);
            $em->flush();
            return $this->redirectToRoute('viewWedstrijduitslagen', array(
                'persoonId' => $persoonId,
                'groepId' => $groepId,
            ));
        }
        return $this->render('inloggen/selectieRemoveWedstrijduitslagen.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'uitslag' => $uitslag,
        ));
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{persoonId}/editTurnster/{groepId}/{turnsterId}/", name="editSelectieTurnster")
     * @Method({"GET", "POST"})
     */
    public function editSelectieTurnsterAction(Request $request, $persoonId, $turnsterId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT persoon
            FROM AppBundle:Persoon persoon
            WHERE persoon.id = :id')
                ->setParameter('id', $turnsterId);
            $result = $query->setMaxResults(1)->getOneOrNullResult();
            $persoonEdit = new \stdClass();
            $persoonEdit->voornaam = $result->getVoornaam();
            $persoonEdit->achternaam = $result->getAchternaam();
            $persoonEdit->geboortedatum = $result->getGeboortedatum();
            $user = $result->getUser();
            $persoonEdit->username = $user->getUsername();
            $persoonEdit->email2 = $user->getEmail2();
            $persoonEdit->userId = $user->getId();
            $persoonEdit->straatnr = $user->getStraatnr();
            $persoonEdit->postcode = $user->getPostcode();
            $persoonEdit->plaats = $user->getPlaats();
            $persoonEdit->tel1 = $user->getTel1();
            $persoonEdit->tel2 = $user->getTel2();
            $persoonEdit->tel3 = $user->getTel3();
            $functies = $result->getFunctie();
            $persoonEdit->functie = array();
            for ($i = 0; $i < count($functies); $i++) {
                $persoonEdit->functie[$i] = new \stdClass();
                $persoonEdit->functie[$i]->functie = $functies[$i]->getFunctie();
                $groep = $functies[$i]->getGroep();
                $persoonEdit->functie[$i]->groepNaam = $groep->getName();
                $persoonEdit->functie[$i]->groepId = $groep->getId();
                $trainingen = $groep->getTrainingen();
                $persoonEdit->functie[$i]->trainingen = array();
                for ($j = 0; $j < count($trainingen); $j++) {
                    $persoonTrainingen = $result->getTrainingen();
                    for ($k = 0; $k < count($persoonTrainingen); $k++) {
                        if ($trainingen[$j]->getId() == $persoonTrainingen[$k]->getId()) {
                            $persoonEdit->functie[$i]->trainingen[$k] = new \stdClass();
                            $persoonEdit->functie[$i]->trainingen[$k]->trainingId = $persoonTrainingen[$k]->getId();
                        }
                    }
                }
            }
            $query = $em->createQuery(
                'SELECT groepen
                FROM AppBundle:Groepen groepen');
            /** @var Groepen $groepen */
            $groepen = $query->getResult();
            $groepenItems = array();
            for ($i = 0; $i < count($groepen); $i++) {
                $groepenItems[$i] = new \stdClass();
                $groepenItems[$i]->id = $groepen[$i]->getId();
                $groepenItems[$i]->naam = $groepen[$i]->getName();
                $groepenItems[$i]->trainingen = array();
                $query = $em->createQuery(
                    'SELECT trainingen
                FROM AppBundle:Trainingen trainingen
                WHERE trainingen.groep = :id')
                    ->setParameter('id', $groepen[$i]->getId());
                $trainingen = $query->getResult();
                for ($j = 0; $j < count($trainingen); $j++) {
                    $groepenItems[$i]->trainingen[$j] = new \stdClass();
                    $groepenItems[$i]->trainingen[$j]->dag = $trainingen[$j]->getDag();
                    $groepenItems[$i]->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdVan();
                    $groepenItems[$i]->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdTot();
                    $groepenItems[$i]->trainingen[$j]->id = $trainingen[$j]->getId();
                }
            }
            if ($request->getMethod() == 'POST') {
                $query = $em->createQuery(
                    'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
                    ->setParameter('id', $turnsterId);

                /** @var Persoon $persoon */
                $persoon = $query->setMaxResults(1)->getOneOrNullResult();
                $persoon->setVoornaam($this->get('request')->request->get('voornaam'));
                $persoon->setAchternaam($this->get('request')->request->get('achternaam'));
                $persoon->setGeboortedatum($this->get('request')->request->get('geboortedatum'));

                /** @var Functie $functie */
                $functies = $persoon->getFunctie();
                foreach ($functies as $functie) {
                    /** @var Groepen $groep */
                    $groep = $functie->getGroep();
                    if (!($this->get('request')->request->get('groep_' . $groep->getId()) == 'Turnster')) {
                        $persoon->removeFunctie($functie);
                        $query = $em->createQuery(
                            'SELECT trainingen
                    FROM AppBundle:Trainingen trainingen
                    WHERE trainingen.groep = :id')
                            ->setParameter('id', $groep->getId());

                        /** @var Trainingen $removeTrainingen */
                        /** @var Trainingen $removeTraining */
                        $removeTrainingen = $query->getResult();
                        foreach ($removeTrainingen as $removeTraining) {
                            $persoon->removeTrainingen($removeTraining);
                        }
                    }
                }

                /** @var Trainingen $trainingen */
                $trainingen = $persoon->getTrainingen();
                foreach ($trainingen as $training) {
                    if (!($this->get('request')->request->get('trainingen_' . $training->getId()) == 'on')) {
                        $persoon->removeTrainingen($training);
                    }
                }

                /** @var \AppBundle\Entity\User $user */
                $user = $persoon->getUser();
                $user->setUsername($this->get('request')->request->get('username'));
                $user->setEmail2($this->get('request')->request->get('email2'));
                $user->setStraatnr($this->get('request')->request->get('straatnr'));
                $user->setPostcode($this->get('request')->request->get('postcode'));
                $user->setPlaats($this->get('request')->request->get('plaats'));
                $user->setTel1($this->get('request')->request->get('tel1'));
                $user->setTel2($this->get('request')->request->get('tel2'));
                $user->setTel3($this->get('request')->request->get('tel3'));

                foreach ($groepen as $groep) {
                    $check = false;
                    if ($this->get('request')->request->get('groep_' . $groep->getId()) == 'Turnster') {
                        foreach ($functies as &$functie) {
                            /** @var Groepen $functieGroep */
                            $functieGroep = $functie->getGroep();
                            if ($functieGroep->getId() == $groep->getId()) {
                                $functie->setFunctie($this->get('request')->request->get('groep_' . $groep->getId()));
                                $check = true;
                            }
                        }
                        if (!$check) {
                            $newFunctie = new Functie();
                            $newFunctie->setFunctie($this->get('request')->request->get('groep_' . $groep->getId()));
                            $newFunctie->setGroep($groep);
                            $newFunctie->setPersoon($persoon);
                            $persoon->addFunctie($newFunctie);
                        }

                        $query = $em->createQuery(
                            'SELECT trainingen
                    FROM AppBundle:Trainingen trainingen
                    WHERE trainingen.groep = :id')
                            ->setParameter('id', $groep->getId());

                        /** @var Trainingen $dbTrainingen */
                        /** @var Trainingen $dbTraining */
                        $dbTrainingen = $query->getResult();
                        foreach ($dbTrainingen as $dbTraining) {
                            $trainingenCheck = false;
                            if ($this->get('request')->request->get('trainingen_' . $dbTraining->getId()) == 'on') {
                                foreach ($trainingen as $training) {
                                    if ($dbTraining->getId() == $training->getId()) {
                                        $trainingenCheck = true;
                                    }
                                }
                                if (!$trainingenCheck) {
                                    $persoon->addTrainingen($dbTraining);
                                }
                            }
                        }
                    }
                }

                $em->persist($persoon);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('showPersoon', array(
                    'id' => $persoonId
                ));
            }
            return $this->render('inloggen/selectieEditTurnster.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'persoon' => $persoon,
                'user' => $user,
                'persoonItems' => $persoonItems,
                'groepen' => $groepenItems,
                'persoonEdit' => $persoonEdit,
                'wedstrijdLinkItems' => $this->groepItems,
                'functie' => $functie,
                'groepId' => $groepId,
                'persoonId' => $persoonId,

            ));
        }
    }

    protected function getSelectieTurnsterInfo($turnsterId, $groepObject, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
            FROM AppBundle:Persoon persoon
            WHERE persoon.id = :id')
            ->setParameter('id', $turnsterId);
        /** @var Persoon $persoonObject */
        $persoonObject = $query->setMaxResults(1)->getOneOrNullResult();
        $turnster = $persoonObject->getAll();
        if ($page == "Index") {
            $imageSize = getimagesize('http://www.donargym.nl/uploads/selectiefotos/' . $turnster->foto);
            $turnster->width = $imageSize[0];
            $turnster->height = $imageSize[1];
        }
        $trainingen = $persoonObject->getTrainingen();
        $userObject = $persoonObject->getUser();
        $turnster->id = $turnsterId;
        $turnster->straatnr = $userObject->getStraatnr();
        $turnster->postcode = $userObject->getPostcode();
        $turnster->plaats = $userObject->getPlaats();
        $turnster->email = $userObject->getUsername();
        $turnster->email2 = $userObject->getEmail2();
        $turnster->tel1 = $userObject->getTel1();
        $turnster->tel2 = $userObject->getTel2();
        $turnster->tel3 = $userObject->getTel3();
        $turnster->trainingen = array();
        $functies = $persoonObject->getFunctie();
        foreach ($functies as $functie) {
            for ($i = 0; $i < count($trainingen); $i++) {
                if (($functie->getGroep() == $trainingen[$i]->getGroep() && $functie->getGroep() == $groepObject)) {
                    $turnster->trainingen[$i] = new \stdClass();
                    $turnster->trainingen[$i]->id = $trainingen[$i]->getId();
                    $turnster->trainingen[$i]->dag = $trainingen[$i]->getDag();
                    $turnster->trainingen[$i]->tijdvan = $trainingen[$i]->getTijdvan();
                    $turnster->trainingen[$i]->tijdtot = $trainingen[$i]->getTijdtot();
                    $turnster->trainingen[$i]->trainingsdata = array();
                    $trainingsdata = $trainingen[$i]->getTrainingsdata();
                    $counter = 0;
                    $aantalTrainingen = 0;
                    $aantalAanwezig = 0;
                    $aanwezigheid = $persoonObject->getAanwezigheid();
                    for ($j = (count($trainingsdata) - 4); $j >= 0; $j--) {
                        $lesdatum = $trainingsdata[$j]->getLesdatum();
                        if (strtotime($lesdatum->format('d-m-Y')) <= time()) {
                            for ($k = (count($aanwezigheid) - 1); $k >= 0; $k--) {
                                $check = false;
                                if (date('m', time()) < '08') {
                                    if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                        ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                    ) {
                                        $check = true;
                                    } else {
                                        break;
                                    }
                                } else {
                                    if ($lesdatum->format('Y') == date('Y', time())) {
                                        if ($lesdatum->format('m') < '08') {
                                            break;
                                        } else {
                                            $check = true;
                                        }
                                    }
                                }
                                if ($check) {
                                    if ($aanwezigheid[$k]->getTrainingsdata() == $trainingsdata[$j]) {
                                        $aantalTrainingen++;
                                        if ($counter < 7) {
                                            $turnster->trainingen[$i]->trainingsdata[$j] = new \stdClass();
                                            $turnster->trainingen[$i]->trainingsdata[$j]->id = $trainingsdata[$j]->getId();
                                            $turnster->trainingen[$i]->trainingsdata[$j]->lesdatum = $lesdatum->format('d-m-Y');
                                            $turnster->trainingen[$i]->trainingsdata[$j]->aanwezigheid = $aanwezigheid[$k]->getAanwezig();
                                            $counter++;
                                        }
                                        if (strtolower($aanwezigheid[$k]->getAanwezig()) == 'x') {
                                            $aantalAanwezig++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $turnster->trainingen[$i]->trainingsdata = array_reverse($turnster->trainingen[$i]->trainingsdata);
                    if ($aantalTrainingen == 0) {
                        $turnster->trainingen[$i]->percentageAanwezig = 100;
                    } else {
                        $turnster->trainingen[$i]->percentageAanwezig = (100 * ($aantalAanwezig / $aantalTrainingen));
                    }
                    $turnster->trainingen[$i]->aantalAanwezig = $aantalAanwezig;
                    $turnster->trainingen[$i]->aantalTrainingen = $aantalTrainingen;
                    $turnster->trainingen[$i]->percentageKleur = $this->colorGenerator($turnster->trainingen[$i]->percentageAanwezig);
                }
            }
        }
        $aantalAanwezig = 0;
        $aantalTrainingen = 0;
        $totaalAanwezigheid = $persoonObject->getAanwezigheid();
        for ($counter = (count($totaalAanwezigheid) - 1); $counter >= 0; $counter--) {
            $check = false;
            /** @var Trainingsdata $trainingsdatum */
            $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
            $lesdatum = $trainingsdatum->getLesdatum();
            /** @var Trainingen $training */
            $training = $trainingsdatum->getTrainingen();
            /** @var Groepen $trainingGroep */
            $trainingGroep = $training->getGroep();
            if ($lesdatum->getTimestamp() <= time() && $trainingGroep->getId() == $groepObject->getId()) {
                if (date('m', time()) < '08') {
                    if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                        ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                    ) {
                        $check = true;
                    } else {
                        break;
                    }
                } else {
                    if ($lesdatum->format('Y') == date('Y', time())) {
                        if ($lesdatum->format('m') < '08') {
                            break;
                        } else {
                            $check = true;
                        }
                    }
                }
            }
            if ($check) {
                $aantalTrainingen++;
                if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                    $aantalAanwezig++;
                }
            }
        }
        if ($aantalTrainingen != 0) {
            $turnster->percentageAanwezig = 100 * $aantalAanwezig / $aantalTrainingen;
        } else {
            $turnster->percentageAanwezig = 100;
        }
        $turnster->percentageKleur = $this->colorGenerator2($turnster->percentageAanwezig);
        $turnster->aantalAanwezig = $aantalAanwezig;
        $turnster->aantalTrainingen = $aantalTrainingen;
        return $turnster;
    }

    protected function getSeizoen($timestamp = null)
    {
        if ($timestamp == null) {
            $timestamp = time();
        }
        if (date('m', $timestamp) > '08') {
            $seizoen = date('Y', $timestamp);
        } else {
            $seizoen = (int)date('Y', $timestamp) - 1;
        }
        return $seizoen;
    }

    protected function getDoelenVoorSeizoen($persoonId, $seizoen)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT seizoensdoelen
            FROM AppBundle:SeizoensDoelen seizoensdoelen
            WHERE seizoensdoelen.persoon = :persoonId
            AND seizoensdoelen.seizoen = :seizoen')
            ->setParameter('persoonId', $persoonId)
            ->setParameter('seizoen', $seizoen);
        $doelen = $query->getResult();
        return ($doelen);
    }

    protected function getDoelDetailsFromIds($ids)
    {
        $doelen = array();
        $doelen['Sprong'] = array();
        $doelen['Brug'] = array();
        $doelen['Balk'] = array();
        $doelen['Vloer'] = array();
        foreach ($ids as $id) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT doelen
            FROM AppBundle:Doelen doelen
            WHERE doelen.id = :id')
            ->setParameter('id', $id);
            /** @var Doelen $doelObject */
            $doelObject = $query->setMaxResults(1)->getOneOrNullResult();
            $doelen[$doelObject->getToestel()][$doelObject->getId()] = $doelObject->getNaam();
        }
        asort($doelen['Sprong']);
        asort($doelen['Brug']);
        asort($doelen['Balk']);
        asort($doelen['Vloer']);
        return $doelen;
    }

    protected function getDoelDetails($doelenObject)
    {
        $doelen = array();
        $doelen['Sprong'] = array();
        $doelen['Brug'] = array();
        $doelen['Balk'] = array();
        $doelen['Vloer'] = array();
        $doelIds = array();
        foreach ($doelenObject as $doelObject) {
            /** @var Doelen $helper */
            $helper = $doelObject->getDoel();
            $doelen[$helper->getToestel()][$helper->getId()] = $helper->getNaam();
            $doelIds[] = $helper->getId();
        }
        asort($doelen['Sprong']);
        asort($doelen['Brug']);
        asort($doelen['Balk']);
        asort($doelen['Vloer']);
        return array($doelen, $doelIds);
    }

    private function getPersoonById($persoonId)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :turnsterId')
            ->setParameter('turnsterId', $persoonId);
        $persoonObject = $query->setMaxResults(1)->getOneOrNullResult();
        return $persoonObject;
    }

    private function getDoelCijfersForTurnster($turnsterId)
    {
        $turnsterObject = $this->getPersoonById($turnsterId);
        $doelenObject = $this->getDoelenVoorSeizoen($turnsterId, $this->getSeizoen());
        $result = $this->getDoelDetails($doelenObject);
        $doelen = $result[0];
        $doelIds = $result[1];
        $cijfers = array();
        $subdoelIds = array();
        $collectedDoelen = array();
        foreach ($doelenObject as $doelObject) {
            $doel = $doelObject->getDoel();
            $cijfers[$doel->getId() . '_hoofd'] = $doelObject->getCijfer();
        }
        $kleuren = array();
        foreach ($cijfers as $DoelId => $percentage) {
            $kleuren[$DoelId] = $this->colorGenerator($percentage);
        }
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :turnsterId')
            ->setParameter('turnsterId', $turnsterId);
        $persoonObject = $query->setMaxResults(1)->getOneOrNullResult();
        if ($persoonObject->getLastUpdatedAtSeizoen() != $this->getSeizoen()) {
            $persoonObject = $this->resetVoortgang($persoonObject);
        }
        $voortgang = array();
        $voortgang['Sprong'] = $persoonObject->getVoortgangSprong();
        $voortgang['Brug'] = $persoonObject->getVoortgangBrug();
        $voortgang['Balk'] = $persoonObject->getVoortgangBalk();
        $voortgang['Vloer'] = $persoonObject->getVoortgangVloer();
        $voortgang['Totaal'] = $persoonObject->getVoortgangTotaal();
        return array($cijfers, $kleuren, $voortgang, $doelen, $doelIds);
    }

    private function getToestelPrijzen($turnsterId)
    {
        $aantalMedailles = 0;
        $aantalBekers = 0;
        $percentages = array(80, 90);
        $em = $this->getDoctrine()->getManager();
        $doelenTachtigProcent = array();
        $doelenNegentigProcent = array();
        $doelenTachtigProcentGegeven = array();
        $doelenNegentigProcentGegeven = array();
        $doelenIds = array();
        $seizoen = $this->getSeizoen();
        foreach ($percentages as $percentage) {
            if ($percentage == 80) {
                $query = $em->createQuery(
                    'SELECT seizoensdoelen
                FROM AppBundle:SeizoensDoelen seizoensdoelen
                WHERE seizoensdoelen.persoon = :turnsterId
                AND seizoensdoelen.cijfer >= :percentage
                AND seizoensdoelen.seizoen = :seizoen
                AND seizoensdoelen.tachtigProcent IS NULL')
                    ->setParameter('turnsterId', $turnsterId)
                    ->setParameter('percentage', $percentage)
                    ->setParameter('seizoen', $seizoen);
                $seizoensdoelenObject = $query->getResult();
                foreach ($seizoensdoelenObject as $seizoensdoelObject) {
                    $doelObject = $seizoensdoelObject->getDoel();
                    $doelenTachtigProcent[$seizoensdoelObject->getId()]['doelNaam'] = $doelObject->getNaam();
                    $doelenTachtigProcent[$seizoensdoelObject->getId()]['toestel'] = $doelObject->getToestel();
                    $doelenTachtigProcent[$seizoensdoelObject->getId()]['cijfer'] = $seizoensdoelObject->getCijfer();
                    $doelenTachtigProcent[$seizoensdoelObject->getId()]['id'] = $seizoensdoelObject->getId();
                    $aantalMedailles++;
                    if (!in_array($seizoensdoelObject->getId(), $doelenIds)) {
                        $doelenIds[] = $seizoensdoelObject->getId();
                    }
                }
            } else {
                $query = $em->createQuery(
                    'SELECT seizoensdoelen
                FROM AppBundle:SeizoensDoelen seizoensdoelen
                WHERE seizoensdoelen.persoon = :turnsterId
                AND seizoensdoelen.cijfer >= :percentage
                AND seizoensdoelen.seizoen = :seizoen
                AND seizoensdoelen.negentigProcent IS NULL')
                    ->setParameter('turnsterId', $turnsterId)
                    ->setParameter('percentage', $percentage)
                    ->setParameter('seizoen', $seizoen);
                $seizoensdoelenObject = $query->getResult();
                foreach ($seizoensdoelenObject as $seizoensdoelObject) {
                    /** @var SeizoensDoelen $seizoensdoelObject */
                    $doelObject = $seizoensdoelObject->getDoel();
                    $doelenNegentigProcent[$seizoensdoelObject->getId()]['doelNaam'] = $doelObject->getNaam();
                    $doelenNegentigProcent[$seizoensdoelObject->getId()]['toestel'] = $doelObject->getToestel();
                    $doelenNegentigProcent[$seizoensdoelObject->getId()]['cijfer'] = $seizoensdoelObject->getCijfer();
                    $doelenNegentigProcent[$seizoensdoelObject->getId()]['id'] = $seizoensdoelObject->getId();
                    $aantalBekers++;
                }
            } if ($percentage == 80) {
                $query = $em->createQuery(
                    'SELECT seizoensdoelen
                FROM AppBundle:SeizoensDoelen seizoensdoelen
                WHERE seizoensdoelen.persoon = :turnsterId
                AND seizoensdoelen.cijfer >= :percentage
                AND seizoensdoelen.seizoen = :seizoen
                AND seizoensdoelen.tachtigProcent = :notnull')
                    ->setParameter('turnsterId', $turnsterId)
                    ->setParameter('percentage', $percentage)
                    ->setParameter('notnull', 1)
                    ->setParameter('seizoen', $seizoen);
                $seizoensdoelenObject = $query->getResult();
                foreach ($seizoensdoelenObject as $seizoensdoelObject) {
                    $doelObject = $seizoensdoelObject->getDoel();
                    $doelenTachtigProcentGegeven[$seizoensdoelObject->getId()]['doelNaam'] = $doelObject->getNaam();
                    $doelenTachtigProcentGegeven[$seizoensdoelObject->getId()]['toestel'] = $doelObject->getToestel();
                    $doelenTachtigProcentGegeven[$seizoensdoelObject->getId()]['cijfer'] = $seizoensdoelObject->getCijfer();
                    $doelenTachtigProcentGegeven[$seizoensdoelObject->getId()]['id'] = $seizoensdoelObject->getId();
                    if (!in_array($seizoensdoelObject->getId(), $doelenIds)) {
                        $doelenIds[] = $seizoensdoelObject->getId();
                    }
                }
            } else {
                $query = $em->createQuery(
                    'SELECT seizoensdoelen
                FROM AppBundle:SeizoensDoelen seizoensdoelen
                WHERE seizoensdoelen.persoon = :turnsterId
                AND seizoensdoelen.cijfer >= :percentage
                AND seizoensdoelen.seizoen = :seizoen
                AND seizoensdoelen.negentigProcent = :notnull')
                    ->setParameter('turnsterId', $turnsterId)
                    ->setParameter('percentage', $percentage)
                    ->setParameter('notnull', 1)
                    ->setParameter('seizoen', $seizoen);
                $seizoensdoelenObject = $query->getResult();
                foreach ($seizoensdoelenObject as $seizoensdoelObject) {
                    /** @var SeizoensDoelen $seizoensdoelObject */
                    $doelObject = $seizoensdoelObject->getDoel();
                    $doelenNegentigProcentGegeven[$seizoensdoelObject->getId()]['doelNaam'] = $doelObject->getNaam();
                    $doelenNegentigProcentGegeven[$seizoensdoelObject->getId()]['toestel'] = $doelObject->getToestel();
                    $doelenNegentigProcentGegeven[$seizoensdoelObject->getId()]['cijfer'] = $seizoensdoelObject->getCijfer();
                    $doelenNegentigProcentGegeven[$seizoensdoelObject->getId()]['id'] = $seizoensdoelObject->getId();
                }
            }
        }
        return array($doelenTachtigProcent, $doelenNegentigProcent, $doelenIds, $aantalMedailles, $aantalBekers,
            $doelenTachtigProcentGegeven, $doelenNegentigProcentGegeven);
    }

    private function updateHoofdDoelCijfersInDatabase($turnsterId)
    {
        $doelenObject = $this->getDoelenVoorSeizoen($turnsterId, $this->getSeizoen());
        $cijfers = array();
        $subdoelIds = array();
        $collectedDoelen = array();
        foreach ($doelenObject as $doelObject) {
            $doel = $doelObject->getDoel();
            $collectedDoelen[] = $doel->getId();
            $array = $this->getDoelOpbouw($doel->getId(), $subdoelIds);
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
            $doelObject->setCijfer($cijfers[$doel->getId() . '_hoofd']);
            $date = date('Y-m-d', time());
            $datum = \DateTime::createFromFormat('Y-m-d', $date);
            $doelObject->setUpdatedCijfersAt($datum);
            $em = $this->getDoctrine()->getManager();
            $em->persist($doelObject);
            $em->flush();
        }
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/viewTurnster/{turnsterId}/{groepId}/", name="viewSelectieTurnster")
     * @Method({"GET"})
     */
    public
    function viewSelectieTurnster($persoonId, $turnsterId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $seizoen = $this->getSeizoen();
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $groepObject = $response['groep'];
            $turnster = $this->getSelectieTurnsterInfo($turnsterId, $groepObject, "Index");
            $response = $this->getDoelCijfersForTurnster($turnsterId);
            $cijfers = $response[0];
            $kleuren = $response[1];
            $voortgang = $response[2];
            $doelen = $response[3];
        }
        return $this->render('inloggen/selectieViewTurnster.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'turnster' => $turnster,
            'doelen' => $doelen,
            'voortgang' => $voortgang,
            'cijfers' => $cijfers,
            'kleuren' => $kleuren,
        ));
    }

    protected function getPercentageVoortgangTotaal($doelen, $cijfers)
    {
        $totaal = 0;
        $voortgang = array();
        foreach ($doelen as $toestel => $doelenData) {
            $toestelSom = 0;
            foreach ($doelenData as $doelId => $doelNaam) {
                $toestelSom += $cijfers[$doelId . '_hoofd'];
            }
            if (count($doelenData) == 0) {
                $voortgang[$toestel] = 100;
                $totaal += $voortgang[$toestel];
                continue;
            }
            $voortgang[$toestel] = $toestelSom/count($doelenData);
            $totaal += $voortgang[$toestel];
        }
        $voortgang['totaal'] = $totaal/4;
        return $voortgang;
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/viewTurnster/{turnsterId}/{groepId}/viewOneDoel/{doelId}/", name="viewSelectieTurnsterOneDoel")
     * @Method({"GET"})
     */
    public
    function viewSelectieTurnsterOneDoel($persoonId, $turnsterId, $groepId, $doelId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $groepObject = $response['groep'];
            $turnster = $this->getSelectieTurnsterInfo($turnsterId, $groepObject);
            $subdoelIds = array();
            $collectedDoelen = array();
            $collectedDoelen[] = $doelId;
            $array = $this->getDoelOpbouw($doelId, $subdoelIds);
            $doelOpbouw = $array[0];
            $subdoelIds = $array[1];
            $result = $this->getSubdoelIds($subdoelIds, $collectedDoelen);
            $subdoelIds = $result[0];
            $extraDoelen = $result[1];
            $cijfers = array();
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
            $kleuren = array();
            $cijfers = $this->getPercentages($doelOpbouw, $cijfers, $turnsterId);
            foreach ($cijfers as $id => $percentage) {
                $kleuren[$id] = $this->colorGenerator($percentage);
            }
        }
        return $this->render('inloggen/selectieViewTurnsterOneDoel.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'turnster' => $turnster,
            'doelOpbouw' => $doelOpbouw,
            'extraDoelen' => $extraDoelen,
            'cijfers' => $cijfers,
            'kleuren' => $kleuren,
        ));
    }

    protected function getSubdoelIds($subdoelIds, $collectedDoelen)
    {
        $extraDoelen = array();
        foreach ($subdoelIds as $subdoelId) {
            if (!in_array($subdoelId, $collectedDoelen)) {
                $collectedDoelen[] = $subdoelId;
                $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                $extraDoelen[$subdoelId] = $array[0];
                $subdoelIds = $array[1];
            }
        }
        foreach ($subdoelIds as $subdoelId) {
            if (!in_array($subdoelId, $collectedDoelen)) {
                $collectedDoelen[] = $subdoelId;
                $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                $extraDoelen[$subdoelId] = $array[0];
                $subdoelIds = $array[1];
            }
        }
        foreach ($subdoelIds as $subdoelId) {
            if (!in_array($subdoelId, $collectedDoelen)) {
                $collectedDoelen[] = $subdoelId;
                $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                $extraDoelen[$subdoelId] = $array[0];
                $subdoelIds = $array[1];
            }
        }
        foreach ($subdoelIds as $subdoelId) {
            if (!in_array($subdoelId, $collectedDoelen)) {
                $collectedDoelen[] = $subdoelId;
                $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                $extraDoelen[$subdoelId] = $array[0];
                $subdoelIds = $array[1];
            }
        }
        return array($subdoelIds, $extraDoelen);
    }

    protected function getPercentages($doel, $cijfers, $turnsterId)
    {
        $subdoelPercentages = array();
        $subdoelen = array_reverse($doel->subdoelen);
        foreach ($subdoelen as $subdoel) {
            $trededoelPercentages = array();
            foreach ($subdoel->trededoelen as $trededoel) {
                if (isset($trededoel->subdoelId)) {
                    if (!isset($cijfers[$trededoel->id . '_hoofd'])) {
                        return false;
                    }
                    $trededoelPercentages[] = $cijfers[$trededoel->id . '_hoofd'];
                    continue;
                } elseif (array_key_exists($trededoel->id, $cijfers)) {
                    $trededoelPercentages[] = $cijfers[$trededoel->id];
                    continue;
                }
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                'SELECT subdoelen
                FROM AppBundle:SubDoelen subdoelen
                WHERE subdoelen.doel = :id
                AND subdoelen.persoon = :turnsterId')
                ->setParameter('id', $trededoel->id)
                ->setParameter('turnsterId', $turnsterId);
                /** @var SubDoelen $subDoelObject */
                $subDoelObject = $query->setMaxResults(1)->getOneOrNullResult();
                $subdoelCijfers = $subDoelObject->getCijfers();
                $som = 0;
                for ($i = 0; $i < 2; $i++) {
                    if ((count($subdoelCijfers)-$i) == 0) break;
                    $som += 10*$subdoelCijfers[$i]->getCijfer();
                }
                if (count($subdoelCijfers) > 2) {
                    for ($i = 2; $i < count($subdoelCijfers); $i++) {
                        if ($subdoelCijfers[$i] != NULL) {
                            $em->remove($subdoelCijfers[$i]);
                            $em->flush();
                        }
                    }
                }

                $trededoelPercentages[] = $som/2;
                $cijfers[$trededoel->id] = $som/2;
            }
            $subdoelPercentages[] = array_sum($trededoelPercentages)/count($trededoelPercentages);
        }


        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT subdoelen
                FROM AppBundle:SubDoelen subdoelen
                WHERE subdoelen.doel = :id
                AND subdoelen.persoon = :turnsterId')
            ->setParameter('id', $doel->id)
            ->setParameter('turnsterId', $turnsterId);
        /** @var SubDoelen $subDoelObject */
        $subDoelObject = $query->setMaxResults(1)->getOneOrNullResult();
        $subdoelCijfers = $subDoelObject->getCijfers();
        $som = 0;
        for ($i = 0; $i < 2; $i++) {
            if ((count($subdoelCijfers)-$i) == 0) break;
            $som += 10*$subdoelCijfers[$i]->getCijfer();
        }
        if (count($subdoelCijfers) > 2) {
            for ($i = 2; $i < count($subdoelCijfers); $i++) {
                if ($subdoelCijfers[$i] != NULL) {
                    $em->remove($subdoelCijfers[$i]);
                    $em->flush();
                }
            }
        }

        $hoofddoelPercentage = $som/2;
        $cijfers[$doel->id] = $som/2;
        $cijfers[$doel->id] = $hoofddoelPercentage;
        if (count($subdoelPercentages) > 0) {
            $doelPercentage = 0.6*(array_sum($subdoelPercentages)/count(($subdoelPercentages))) + 0.4*$hoofddoelPercentage;
        } else {
            $doelPercentage = $hoofddoelPercentage;
        }
        $cijfers[$doel->id . '_hoofd'] = $doelPercentage;
        return ($cijfers);
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{persoonId}/viewTurnster/{turnsterId}/{groepId}/cijferGeven/", name="SelectieTurnsterAddCijfer")
     * @Method({"GET", "POST"})
     */
    public
    function SelectieTurnsterAddCijfer($persoonId, $turnsterId, $groepId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $seizoen = $this->getSeizoen();
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $groepObject = $response['groep'];
            $token = $this->getToken();
            $repeat = false;
            $turnster = $this->getSelectieTurnsterInfo($turnsterId, $groepObject);
            $doelenObject = $this->getDoelenVoorSeizoen($turnsterId, $seizoen);
            $ids = $this->getAssignedDoelen($doelenObject);
            $allSubdoelen = $this->getDoelDetailsFromIds($ids);
            if ($request->getMethod() == 'POST') {
                $postedToken = $request->request->get('token');
                if (!empty($postedToken)) {
                    if ($this->isTokenValid($postedToken)) {
                        $toestellen = array('Sprong', 'Brug', 'Balk', 'Vloer');
                        $em = $this->getDoctrine()->getManager();
                        foreach ($toestellen as $toestel) {
                            if ($request->request->get('doel_' . $toestel) != '') {
                                $query = $em->createQuery(
                                    'SELECT subdoelen
                                FROM AppBundle:SubDoelen subdoelen
                                WHERE subdoelen.doel = :id
                                AND subdoelen.persoon = :turnsterId')
                                    ->setParameter('id', $request->request->get('doel_' . $toestel))
                                    ->setParameter('turnsterId', $turnsterId);
                                $subDoelObject = $query->setMaxResults(1)->getOneOrNullResult();
                                $counter = 1;
                                if ($request->request->get('twee_keer_' . $toestel)) {
                                    $counter = 2;
                                }
                                while ($counter > 0) {
                                    $cijfer = new Cijfers();
                                    $cijfer->setCijfer($request->request->get('cijfer_' . $toestel));
                                    $cijfer->setSubdoel($subDoelObject);
                                    $cijfer->setDate(new \DateTime('NOW'));
                                    $em->persist($cijfer);
                                    $em->flush();
                                    $counter--;
                                }
                            }
                        }
                        $query = $em->createQuery(
                            'SELECT persoon
                            FROM AppBundle:Persoon persoon
                            WHERE persoon.id = :turnsterId')
                            ->setParameter('turnsterId', $turnsterId);
                        /** @var Persoon $turnster */
                        $turnster = $query->setMaxResults(1)->getOneOrNullResult();
                        $this->updateDoelCijfersInDatabase($turnster);
                        $this->updateHoofdDoelCijfersInDatabase($turnsterId);
                    }
                    if ($request->request->get('repeat')) {
                        $repeat = true;
                    } else {
                        return $this->redirectToRoute('viewSelectieTurnster', array(
                            'persoonId' => $persoonId,
                            'turnsterId' => $turnsterId,
                            'groepId' => $groepId,
                        ));
                    }
                }
            }
        }
        return $this->render('inloggen/SelectieTurnsterAddCijfer.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'turnster' => $turnster,
            'doelen' => $allSubdoelen,
            'repeat' => $repeat,
            'token' => $token,
        ));
    }

    protected function getAssignedDoelen($doelenObject)
    {
        $ids = array();
        foreach ($doelenObject as $doelObject) {
            /** @var SeizoensDoelen $doelObject */
            $doel = $doelObject->getDoel();
            $ids[] = $doel->getId();
        }
        $subdoelIds = array();
        foreach ($ids as $id) {
            $result = $this->getDoelOpbouw($id, $subdoelIds, $ids);
            $ids = $result[2];
        }
        foreach ($ids as $id) {
            $result = $this->getDoelOpbouw($id, $subdoelIds, $ids);
            $ids = $result[2];
        }
        foreach ($ids as $id) {
            $result = $this->getDoelOpbouw($id, $subdoelIds, $ids);
            $ids = $result[2];
        }
        foreach ($ids as $id) {
            $result = $this->getDoelOpbouw($id, $subdoelIds, $ids);
            $ids = $result[2];
        }
        foreach ($ids as $id) {
            $result = $this->getDoelOpbouw($id, $subdoelIds, $ids);
            $ids = $result[2];
        }
        foreach ($ids as $id) {
            $result = $this->getDoelOpbouw($id, $subdoelIds, $ids);
            $ids = $result[2];
        }
        sort($ids);
        return ($ids);
    }

    protected function getAvailableDoelen($doelenIds)
    {
        $doelenLijst = array();
        $doelenLijst['Sprong'] = array();
        $doelenLijst['Brug'] = array();
        $doelenLijst['Balk'] = array();
        $doelenLijst['Vloer'] = array();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT doelen
            FROM AppBundle:Doelen doelen
            WHERE doelen.trede IS NULL');
        $doelenObject = $query->getResult();
        foreach ($doelenObject as $doelObject) {
            if (isset($doelenIds)) if (in_array($doelObject->getId(), $doelenIds)) continue;
            $toestel = $doelObject->getToestel();
            $doelenLijst[$toestel][$doelObject->getId()] = $doelObject->getNaam();
        }
        $toestellen = array ('Sprong', 'Brug', 'Balk', 'Vloer');
        foreach ($toestellen as $toestel) {
            asort($doelenLijst[$toestel]);
        }
        return $doelenLijst;
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/addDoelToTurnster/{groepId}/{turnsterId}/", name="addDoelToTurnster")
     * @Method({"GET", "POST"})
     */
    public function addDoelToTurnster($persoonId, $groepId, $turnsterId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $repeat = false;
            $groepObject = $response['groep'];
            $turnster = $this->getSelectieTurnsterInfo($turnsterId, $groepObject);
            $token = $this->getToken();
            if ($request->getMethod() == 'POST') {
                $postedToken = $request->request->get('token');
                if (!empty($postedToken)) {
                    if ($this->isTokenValid($postedToken)) {
                        $toestellen = array('Sprong', 'Brug', 'Balk', 'Vloer');
                        $em = $this->getDoctrine()->getManager();
                        foreach ($toestellen as $toestel) {
                            $query = $em->createQuery(
                                'SELECT doelen
                        FROM AppBundle:Doelen doelen
                        WHERE doelen.id = :id')
                                ->setParameter('id', $request->request->get('doel_' . $toestel));
                            $doelObject = $query->setMaxResults(1)->getOneOrNullResult();
                            if (count($doelObject) == 1) {
                                $query = $em->createQuery(
                                    'SELECT persoon
                                FROM AppBundle:Persoon persoon
                                WHERE persoon.id = :id')
                                    ->setParameter('id', $turnsterId);
                                $turnserObject = $query->setMaxResults(1)->getOneOrNullResult();

                                $seizoensDoel = new SeizoensDoelen();
                                $seizoensDoel->setDoel($doelObject);
                                $seizoensDoel->setPersoon($turnserObject);
                                $seizoen = $this->getSeizoen();
                                $seizoensDoel->setSeizoen($seizoen);
                                $em->persist($seizoensDoel);
                            }
                        }
                        $em->flush();

                        $this->updateDoelCijfersInDatabase($turnserObject);
                        $this->updateHoofdDoelCijfersInDatabase($turnsterId);

                        if ($request->request->get('repeat')) {
                            $repeat = true;
                        } else {
                            return $this->redirectToRoute('viewSelectieTurnster', array(
                                'persoonId' => $persoonId,
                                'turnsterId' => $turnsterId,
                                'groepId' => $groepId,
                            ));
                        }
                    }
                }
            }
            $seizoen = $this->getSeizoen();
            $doelenObject = $this->getDoelenVoorSeizoen($turnsterId, $seizoen);
            $ids = $this->getAssignedDoelen($doelenObject);
            $doelenLijst = $this->getAvailableDoelen($ids);
        }
        return $this->render('inloggen/selectieAddDoelToTurnster.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'turnster' => $turnster,
            'doelen' => $doelenLijst,
            'token' => $token,
            'repeat' => $repeat,
        ));
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/addDoelToTurnster/{groepId}/{turnsterId}/{doelId}/", name="removeDoelFromTurnster")
     * @Method({"GET", "POST"})
     */
    public function removeDoelFromTurnster($persoonId, $groepId, $turnsterId, $doelId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $groepObject = $response['groep'];
            $turnster = $this->getSelectieTurnsterInfo($turnsterId, $groepObject);
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
            'SELECT seizoensdoelen
            FROM AppBundle:SeizoensDoelen seizoensdoelen
            WHERE seizoensdoelen.doel = :id
            AND seizoensdoelen.persoon = :turnsterId')
            ->setParameter('id', $doelId)
            ->setParameter('turnsterId', $turnsterId);
            /** @var SeizoensDoelen $seizoensDoel */
            $seizoensDoel = $query->setMaxResults(1)->getOneOrNullResult();
            if ($request->getMethod() == 'POST') {
                $em->remove($seizoensDoel);
                $em->flush();
                $query = $em->createQuery(
                    'SELECT persoon
                    FROM AppBundle:Persoon persoon
                    WHERE persoon.id = :turnsterId')
                    ->setParameter('turnsterId', $turnsterId);
                $turnster = $query->setMaxResults(1)->getOneOrNullResult();
                $this->updateDoelCijfersInDatabase($turnster);
                return $this->redirectToRoute('viewSelectieTurnster', array(
                    'persoonId' => $persoonId,
                    'turnsterId' => $turnsterId,
                    'groepId' => $groepId,
                ));
            }
        }
        $doelObject = $seizoensDoel->getDoel();
        $doelNaam = $doelObject->getNaam();
        $doelToestel = $doelObject->getToestel();
        return $this->render('inloggen/selectieRemoveDoelFromTurnster.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'turnster' => $turnster,
            'doelNaam' => $doelNaam,
            'doelToestel' => $doelToestel,
        ));
    }

    protected function getVloermuziekjes($groepObject)
    {
        /** @var Groepen $groepObject */
        $vloermuziek = array();
        $personen = $groepObject->getPeople();
        for ($i = 0; $i < count($personen); $i++) {
            $functies = $personen[$i]->getFunctie();
            foreach ($functies as $functie) {
                if ($functie->getFunctie() == 'Turnster' && $functie->getGroep() == $groepObject) {
                    $persoonInfo = $personen[$i]->getAll();
                    if ($persoonInfo->categorie == 'Jeugd 2' || $persoonInfo->categorie == 'Junior' || $persoonInfo->categorie == 'Senior') {
                        $vloermuziek[$i] = $persoonInfo;
                    }
                }
            }
        }
        return $vloermuziek;
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{persoonId}/viewvloermuziek/{groepId}/", name="viewVloermuziek")
     * @Method({"GET"})
     */
    public function viewVloermuziek($persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $groepObject = $response['groep'];
            $vloermuziek = $this->getVloermuziekjes($groepObject);
        }
        return $this->render('inloggen/selectieViewVloermuziek.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'vloermuziek' => $vloermuziek,
        ));
    }

    /**
     * @Template()
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{persoonId}/addVloermuziek/{groepId}/{turnsterId}/", name="addSelectieVloermuziekPage")
     * @Method({"GET", "POST"})
     */
    public
    function addSelectieVloermuziekPageAction(Request $request, $persoonId, $turnsterId, $groepId)
    {
        $error = null;
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer', 'Turnster');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            if (($functie == 'Turnster' && $persoonId == $turnsterId) || $functie == 'Trainer') {
                $vloermuziek = new Vloermuziek();
                /** @var Groepen $groepObject */
                $groepObject = $response['groep'];
                $personen = $groepObject->getPeople();
                foreach ($personen as $persoonObj) {
                    if ($persoonObj->getId() == $turnsterId) {
                        $persoonObject = $persoonObj;
                        /** @var Persoon $persoonObject */
                        $turnster = new \stdClass();
                        $turnster->id = $persoonObject->getId();
                        $turnster->voornaam = $persoonObject->getVoornaam();
                        $turnster->achternaam = $persoonObject->getAchternaam();
                        break;
                    }
                }
                $form = $this->createFormBuilder($vloermuziek)
                    ->add('file')
                    ->add('uploadBestand', 'submit')
                    ->getForm();
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $extensions = array('mp3', 'wma');
                    if (in_array(strtolower($vloermuziek->getFile()->getClientOriginalExtension()), $extensions)) {
                        $persoonObject->setVloermuziek($vloermuziek);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($persoonObject);
                        $em->flush();
                        if ($persoonId == $turnsterId) {
                            return $this->redirectToRoute('showPersoon', array(
                                'id' => $persoonId
                            ));
                        }
                        return $this->redirectToRoute('viewVloermuziek', array(
                            'persoonId' => $persoonId,
                            'groepId' => $groepId,
                        ));
                    } else {
                        $error = 'Please upload a valid audio file: mp3 or wma';
                    }
                }
                return $this->render('inloggen/selectieAddVloermuziek.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'form' => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                    'persoon' => $persoon,
                    'user' => $user,
                    'persoonItems' => $persoonItems,
                    'turnster' => $turnster,
                    'error' => $error,
                    'functie' => $functie,
                    'groepId' => $groepId,
                ));

            }
        }
    }

    protected function getAllDoelen($viewDoelen = true)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT doelen
            FROM AppBundle:Doelen doelen');
        $doelenObject = $query->getResult();
        $doelenPerToestel = array();
        $doelenPerToestel[0] = new \stdClass();
        $doelenPerToestel[0]->naam = 'Sprong';
        $doelenPerToestel[0]->doelen = array();
        $doelenPerToestel[1] = new \stdClass();
        $doelenPerToestel[1]->naam = 'Brug';
        $doelenPerToestel[1]->doelen = array();
        $doelenPerToestel[2] = new \stdClass();
        $doelenPerToestel[2]->naam = 'Balk';
        $doelenPerToestel[2]->doelen = array();
        $doelenPerToestel[3] = new \stdClass();
        $doelenPerToestel[3]->naam = 'Vloer';
        $doelenPerToestel[3]->doelen = array();
        for ($i = 0; $i < count($doelenObject); $i++) {
            if ($viewDoelen) {
                if ($doelenObject[$i]->getTrede()) {
                    continue;
                }
            }
            switch ($doelenObject[$i]->getToestel()) {
                case 'Sprong':
                    $doelenPerToestel[0]->doelen[$i] = new \stdClass();
                    $doelenPerToestel[0]->doelen[$i]->id = $doelenObject[$i]->getId();
                    $doelenPerToestel[0]->doelen[$i]->naam = $doelenObject[$i]->getNaam();
                    $doelenPerToestel[0]->doelen[$i]->trede = $doelenObject[$i]->getTrede();
                    break;
                case 'Brug':
                    $doelenPerToestel[1]->doelen[$i] = new \stdClass();
                    $doelenPerToestel[1]->doelen[$i]->id = $doelenObject[$i]->getId();
                    $doelenPerToestel[1]->doelen[$i]->naam = $doelenObject[$i]->getNaam();
                    $doelenPerToestel[1]->doelen[$i]->trede = $doelenObject[$i]->getTrede();
                    break;
                case 'Balk':
                    $doelenPerToestel[2]->doelen[$i] = new \stdClass();
                    $doelenPerToestel[2]->doelen[$i]->id = $doelenObject[$i]->getId();
                    $doelenPerToestel[2]->doelen[$i]->naam = $doelenObject[$i]->getNaam();
                    $doelenPerToestel[2]->doelen[$i]->trede = $doelenObject[$i]->getTrede();
                    break;
                case 'Vloer':
                    $doelenPerToestel[3]->doelen[$i] = new \stdClass();
                    $doelenPerToestel[3]->doelen[$i]->id = $doelenObject[$i]->getId();
                    $doelenPerToestel[3]->doelen[$i]->naam = $doelenObject[$i]->getNaam();
                    $doelenPerToestel[3]->doelen[$i]->trede = $doelenObject[$i]->getTrede();
                    break;
            }
        }
        for ($i=0;$i<count($doelenPerToestel);$i++) {
            usort($doelenPerToestel[$i]->doelen, function($a, $b)
            {
                return strcmp($a->naam, $b->naam);
            });
        }
        return $doelenPerToestel;
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/viewdoelen/{groepId}/", name="viewDoelen")
     * @Method({"GET"})
     */
    public function viewDoelen($persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $doelenPerToestel = $this->getAllDoelen();
        }
        return $this->render('inloggen/selectieViewDoelen.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'doelenPerToestel' => $doelenPerToestel,
        ));
    }

    /**
     * @Security("has_role('ROLE_TRAINER')")
     * @Route("/inloggen/selectie/{persoonId}/adddoelen/{groepId}/", name="addDoelen")
     * @Method({"GET", "POST"})
     */
    public function addDoelen($persoonId, $groepId, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $doelenPerToestel = $this->getAllDoelen(false);
            $repeat = false;
            if ($request->getMethod() == 'POST') {
                $doel = new Doelen();
                $doel->setNaam($request->request->get('naam'));
                $doel->setToestel($request->request->get('toestel'));
                $subdoelen = array();
                $em = $this->getDoctrine()->getManager();
                if ($request->request->get('sub1')) {
                    $subdoelen[] = $request->request->get('sub1');
                } if ($request->request->get('sub2')) {
                    $subdoelen[] = $request->request->get('sub2');
                } if ($request->request->get('sub3')) {
                    $subdoelen[] = $request->request->get('sub3');
                } if ($request->request->get('sub4')) {
                    $subdoelen[] = $request->request->get('sub4');
                } if ($request->request->get('sub5')) {
                    $subdoelen[] = $request->request->get('sub5');
                } if ($request->request->get('sub6')) {
                    $subdoelen[] = $request->request->get('sub6');
                } if (count($subdoelen) > 0) {
                    $doel->setSubdoelen(json_encode($subdoelen));
                } if ($request->request->get('trede')) {
                    $doel->setTrede($request->request->get('trede'));
                    $em->persist($doel);
                } else {
                    $em->persist($doel);
                    $query = $em->createQuery(
                    'SELECT persoon
                    FROM AppBundle:Persoon persoon');
                    $personen = $query->getResult();
                    /** @var Persoon $persoon */
                    foreach ($personen as $persoon) {
                        $subdoelEntity = new SubDoelen();
                        $subdoelEntity->setDoel($doel);
                        $subdoelEntity->setPersoon($persoon);
                        $em->persist($subdoelEntity);
                        $em->flush();
                    }
                }
                $em->flush();
                if ($request->request->get('repeat')) {
                    $repeat = true;
                    $doelenPerToestel = $this->getAllDoelen(false);
                } else {
                    return $this->redirectToRoute('viewDoelen', array(
                        'persoonId' => $persoonId,
                        'groepId' => $groepId,
                    ));
                }
            }
        }
        return $this->render('inloggen/selectieAddDoelen.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'doelenPerToestel' => $doelenPerToestel,
            'repeat' => $repeat,
        ));
    }

    protected function getDoelOpbouw($doelId, $subDoelIds, $ids = array())
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT doelen
            FROM AppBundle:Doelen doelen
            WHERE doelen.id = :id')
            ->setParameter('id', $doelId);
        /** @var Doelen $doelObject */
        $doelObject = $query->setMaxResults(1)->getOneOrNullResult();
        $doelOpbouw = new \stdClass();
        $doelOpbouw->naam = $doelObject->getNaam();
        $doelOpbouw->toestel = $doelObject->getToestel();
        $doelOpbouw->id = $doelId;
        if (!in_array($doelId, $ids)) $ids[] = $doelId;
        $doelOpbouw->subdoelen = array();
        while (true) {
            if ($doelObject->getTrede()) {
                if(!isset($hoofddoel)) {
                    $hoofddoel = new \stdClass();
                    $hoofddoel->naam = $doelObject->getNaam();
                    $hoofddoel->toestel = $doelObject->getToestel();
                }
                $trede = explode(' ', $doelObject->getTrede());
                $trede = $trede[1];
                for ($trede; $trede > 0; $trede--) {
                    $query = $em->createQuery(
                        'SELECT doelen
                    FROM AppBundle:Doelen doelen
                    WHERE doelen.naam = :naam
                    AND doelen.trede = :trede
                    AND doelen.toestel = :toestel')
                        ->setParameter('naam', $hoofddoel->naam)
                        ->setParameter('trede', 'Trede ' . $trede)
                        ->setParameter('toestel', $hoofddoel->toestel);
                    /** @var Doelen $subdoelObject */
                    $subdoelObject = $query->setMaxResults(1)->getOneOrNullResult();
                    $subdoelenArray = json_decode($subdoelObject->getSubdoelen());
                    $doelOpbouw->subdoelen[$trede] = new\stdClass();
                    $doelOpbouw->subdoelen[$trede]->trededoelen = array();
                    for ($j = 0; $j < count($subdoelenArray); $j++) {
                        $query = $em->createQuery(
                            'SELECT doelen
                        FROM AppBundle:Doelen doelen
                        WHERE doelen.id = :id')
                            ->setParameter('id', $subdoelenArray[$j]);
                        /** @var Doelen $trededoelObject */
                        $trededoelObject = $query->setMaxResults(1)->getOneOrNullResult();
                        $doelOpbouw->subdoelen[$trede]->trededoelen[$j] = new \stdClass();
                        $doelOpbouw->subdoelen[$trede]->trededoelen[$j]->id = $trededoelObject->getId();
                        $doelOpbouw->subdoelen[$trede]->trededoelen[$j]->naam = $trededoelObject->getNaam();
                        $doelOpbouw->subdoelen[$trede]->trededoelen[$j]->toestel = $trededoelObject->getToestel();
                        if (!in_array($trededoelObject->getId(), $ids)) $ids[] = $trededoelObject->getId();
                        $subsubdoelenIds = json_decode($trededoelObject->getSubdoelen());
                        if (count($subsubdoelenIds) > 0) {
                            $query = $em->createQuery(
                            'SELECT doelen
                            FROM AppBundle:Doelen doelen
                            WHERE doelen.id = :id')
                            ->setParameter('id', $subsubdoelenIds[0]);
                            /** @var Doelen $subsubdoelObject */
                            $subsubdoelObject = $query->setMaxResults(1)->getOneOrNullResult();
                            $subsubdoeltrede = explode(' ', $subsubdoelObject->getTrede());
                            if (count($subsubdoeltrede) != 2) {
                                $doelOpbouw->subdoelen[$trede]->trededoelen[$j]->subdoelId = $trededoelObject->getId();
                                if (!in_array($trededoelObject->getId(), $subDoelIds)) {
                                    $subDoelIds[] = $trededoelObject->getId();
                                }
                            }
                            if (count($subsubdoeltrede) == 2) {
                                if (!($subsubdoeltrede[1] == ($trede - 1) && $subsubdoelObject->getToestel() == $hoofddoel->toestel
                                    && $subsubdoelObject->getNaam() == $hoofddoel->naam)) {
                                    $doelOpbouw->subdoelen[$trede]->trededoelen[$j]->subdoelId = $trededoelObject->getId();
                                    if (!in_array($trededoelObject->getId(), $subDoelIds)) {
                                        $subDoelIds[] = $trededoelObject->getId();
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            } else {
                $subdoelenArray = json_decode($doelObject->getSubdoelen());
                if (count($subdoelenArray) == 0) {
                    break;
                } else {
                    $query = $em->createQuery(
                        'SELECT doelen
                    FROM AppBundle:Doelen doelen
                    WHERE doelen.id = :id')
                        ->setParameter('id', $subdoelenArray[0]);
                    /** @var Doelen $doelObject */
                    $doelObject = $query->setMaxResults(1)->getOneOrNullResult();
                    $hoofddoel = new \stdClass();
                    $hoofddoel->naam = $doelObject->getNaam();
                    $hoofddoel->toestel = $doelObject->getToestel();
                }
            }
        }
        //var_dump($doelOpbouw);die;
        return array($doelOpbouw, $subDoelIds, $ids);
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/viewonedoel/{groepId}/{doelId}/", name="viewOneDoel")
     * @Method({"GET"})
     */
    public function viewOneDoel($persoonId, $groepId, $doelId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        $roles = array('Trainer', 'Assistent-Trainer');
        $response = $this->checkGroupAuthorization($userObject, $persoonId, $groepId, $roles);
        if ($response['authorized']) {
            $functie = $response['functie'];
            $subdoelIds = array();
            $collectedDoelen = array();
            $collectedDoelen[] = $doelId;
            $array = $this->getDoelOpbouw($doelId, $subdoelIds);
            $doelOpbouw = $array[0];
            $subdoelIds = $array[1];
            $extraDoelen = array();
            foreach ($subdoelIds as $subdoelId) {
                if (!in_array($subdoelId, $collectedDoelen)) {
                    $collectedDoelen[] = $subdoelId;
                    $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                    $extraDoelen[$subdoelId] = $array[0];
                    $subdoelIds = $array[1];
                }
            }
            foreach ($subdoelIds as $subdoelId) {
                if (!in_array($subdoelId, $collectedDoelen)) {
                    $collectedDoelen[] = $subdoelId;
                    $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                    $extraDoelen[$subdoelId] = $array[0];
                    $subdoelIds = $array[1];
                }
            }
            foreach ($subdoelIds as $subdoelId) {
                if (!in_array($subdoelId, $collectedDoelen)) {
                    $collectedDoelen[] = $subdoelId;
                    $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                    $extraDoelen[$subdoelId] = $array[0];
                    $subdoelIds = $array[1];
                }
            }
            foreach ($subdoelIds as $subdoelId) {
                if (!in_array($subdoelId, $collectedDoelen)) {
                    $collectedDoelen[] = $subdoelId;
                    $array = $this->getDoelOpbouw($subdoelId, $subdoelIds);
                    $extraDoelen[$subdoelId] = $array[0];
                    $subdoelIds = $array[1];
                }
            }
        }
        return $this->render('inloggen/selectieViewOneDoel.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' => $groepId,
            'doelOpbouw' => $doelOpbouw,
            'extraDoelen' => $extraDoelen,
        ));
    }
}
