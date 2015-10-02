<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Aanwezigheid;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FotoUpload;
use AppBundle\Entity\Functie;
use AppBundle\Entity\Groepen;
use AppBundle\Entity\Persoon;
use AppBundle\Entity\SelectieFoto;
use AppBundle\Entity\Stukje;
use AppBundle\Entity\Trainingen;
use AppBundle\Entity\Trainingsdata;
use AppBundle\Entity\Vloermuziek;
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
     * @Route("/inloggen/selectie/", name="getSelectieIndexPage")
     * @Method({"GET"})
     * @Security("has_role('ROLE_TURNSTER')")
     */
    public function getSelectieIndexPage()
    {
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
        $this->header = 'wedstrijdturnen' . rand(1, 12);
        $this->calendarItems = $this->getCalendarItems();
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

    private function getBasisUserGegevens($userObject)
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
    private function getBasisPersoonsGegevens($userObject)
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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

    private function dayToDutch($time)
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

    private function getOnePersoon($userObject, $id, $afmelden = false)
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
                if ($vloermuziek == null) {$persoonItems->vloermuziek = null;}
                else {$persoonItems->vloermuziek = $vloermuziek->getLocatie();}
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
                    if($persoonItems->functies[$i]->functie == 'Turnster') {
                        $stukje = $persoon->getStukje();
                        $persoonItems->stukje = $stukje->getAll();
                    }

                    $aantalAanwezig = 0;
                    $aantalTrainingen = 0;
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
                        if ($lesdatum->getTimestamp() <= time() && $trainingGroep->getId() == $persoonItems->functies[$i]->groepId) {
                            if (date('m', time()) < '08') {
                                if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                    ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')) {
                                    $check = true;
                                } else {
                                    break;
                                }
                            } else {
                                if($lesdatum->format('Y') == date('Y', time())) {
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
                        $persoonItems->functies[$i]->percentageAanwezig = 100 * $aantalAanwezig / $aantalTrainingen;
                    } else {
                        $persoonItems->functies[$i]->percentageAanwezig = 100;
                    }
                    $persoonItems->functies[$i]->percentageKleur = $this->colorGenerator($persoonItems->functies[$i]->percentageAanwezig);
                    $persoonItems->functies[$i]->aantalAanwezig = $aantalAanwezig;
                    $persoonItems->functies[$i]->aantalTrainingen = $aantalTrainingen;

                    $groepFuncties = $groep->getFuncties();
                    for ($j = 0; $j < count($groepFuncties); $j++) {
                        if ($groepFuncties[$j]->getFunctie() == 'Turnster') {
                            $persoonItems->functies[$i]->turnster[$j] = new \stdClass();
                            /** @var Persoon $turnster */
                            $turnster = $groepFuncties[$j]->getPersoon();

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
                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')) {
                                            $check = true;
                                        } else {
                                            break;
                                        }
                                    } else {
                                        if($lesdatum->format('Y') == date('Y', time())) {
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
                                        if($lesdatum->format('Y') == date('Y', time())) {
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
                                        if($lesdatum->format('Y') == date('Y', time())) {
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
                }
                /** @var Trainingen $trainingen */
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
                                foreach($aanwezigheid as $aanwezig) {
                                    if($aanwezig->getTrainingsdata() == $trainingsdata[$j]) {
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
                        $counter=0;
                        $aantalTrainingen = 0;
                        $aantalAanwezig = 0;
                        $aanwezigheid = $persoon->getAanwezigheid();
                        for ($j = (count($trainingsdata) - 4); $j >= 0; $j--) {
                            $lesdatum = $trainingsdata[$j]->getLesdatum();
                            if (strtotime($lesdatum->format('d-m-Y')) <= time()) {
                                for ($k = (count($aanwezigheid) - 1); $k >= 0; $k--) {
                                    $check = false;
                                    if(date('m', time()) < '08') {
                                        if(($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')) {
                                            $check = true;
                                        } else {
                                            break;
                                        }
                                    } else {
                                        if($lesdatum->format('Y') == date('Y', time())) {
                                            if ($lesdatum->format('m') < '08') {
                                                break;
                                            } else {
                                                $check = true;
                                            }
                                        }
                                    }
                                    if($check) {
                                        if ($aanwezigheid[$k]->getTrainingsdata() == $trainingsdata[$j]) {
                                            $aantalTrainingen++;
                                            if($counter < 7) {
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
                    // TODO: doelen
                }
                return ($persoonItems);
            }
        }
        /*var_dump($persoonItems);
        die;*/
    }

    private function getPersoonObject($userObject, $id)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if ($persoon->getId() == $id) {
                return $persoon;
            }
        }
    }

    private function colorGenerator($percentage)
    {
        if($percentage>=100) {return '00FF00';} //Green
        elseif($percentage>=99) {return '11FF00';}
        elseif($percentage>=97) {return '22FF00';}
        elseif($percentage>=96) {return '33FF00';}
        elseif($percentage>=94) {return '44FF00';}
        elseif($percentage>=93) {return '55FF00';}
        elseif($percentage>=91) {return '66FF00';}
        elseif($percentage>=90) {return '77FF00';}
        elseif($percentage>=88) {return '88FF00';}
        elseif($percentage>=87) {return '99FF00';}
        elseif($percentage>=85) {return 'AAFF00';}
        elseif($percentage>=84) {return 'BBFF00';}
        elseif($percentage>=82) {return 'CCFF00';}
        elseif($percentage>=81) {return 'DDFF00';}
        elseif($percentage>=79) {return 'EEFF00';}
        elseif($percentage>=78) {return 'FFFF00';} //Yellow
        elseif($percentage>=75) {return 'FFEE00';}
        elseif($percentage>=70) {return 'FFDD00';}
        elseif($percentage>=65) {return 'FFCC00';}
        elseif($percentage>=60) {return 'FFBB00';}
        elseif($percentage>=55) {return 'FFAA00';}
        elseif($percentage>=50) {return 'FF9900';}
        elseif($percentage>=45) {return 'FF8800';}
        elseif($percentage>=40) {return 'FF7700';}
        elseif($percentage>=35) {return 'FF6600';}
        elseif($percentage>=30) {return 'FF5500';}
        elseif($percentage>=25) {return 'FF4400';}
        elseif($percentage>=20) {return 'FF3300';}
        elseif($percentage>=15) {return 'FF2200';}
        elseif($percentage>=10) {return 'FF1100';}
        else {return 'FF0000';} //Red
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{id}/", name="showPersoon")
     * @Method({"GET"})
     */
    public
    function showPersoon($id)
    {
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id);
        return $this->render('inloggen/selectieShowPersoon.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
        /** @var \AppBundle\Entity\User $userObject */
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $id, true);
        if ($request->getMethod() == 'POST') {
            /** @var Persoon $persoonObject */
            $persoonObject = $this->getPersoonObject($userObject, $id);
            $afmeldingsData = array();
            foreach ($_POST as $key=>$value) {
                if($key != "reden") {
                    $em = $this->getDoctrine()->getManager();
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
                }
                else {
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
                    ->setFrom($_SESSION['username'])
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

                if($user->getEmail2()) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Afmelding ' . $persoonItems->voornaam . $persoonItems->achternaam)
                        ->setFrom($_SESSION['username'])
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
        return $this->render('inloggen/selectieAfmelden.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
            'groepId' => $groepId,
        ));
    }

    private function getAanwezigheid($userObject, $id, $groepId, $toekomst) {
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
                            for ($i=0; $i<count($trainingen); $i++) {
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
                                for ($j=0; $j<count($personenPerTraining); $j++) {
                                    /** @var Functie $functiePerPersoon */
                                    $functiesPerPersoon = $personenPerTraining[$j]->getFunctie();
                                    foreach ($functiesPerPersoon as $functiePerPersoon) {
                                        $groepPerPersoon = $functiePerPersoon->getGroep();
                                        if($groepPerPersoon->getId() == $groepId) {
                                            $aantalTrainingen = 0;
                                            $aantalAanwezig = 0;
                                            $aanwezighedenPerPersoon = $personenPerTraining[$j]->getAanwezigheid();
                                            for ($jj=(count($aanwezighedenPerPersoon)-1); $jj>=0; $jj--) {
                                                /** @var Aanwezigheid $aanwezigheidPerPersoon */
                                                $aanwezigTrainingsdata = $aanwezighedenPerPersoon[$jj]->getTrainingsdata();
                                                $lesdatum = $aanwezigTrainingsdata->getLesdatum();
                                                $timestamp = $lesdatum->getTimestamp();
                                                if($aanwezigTrainingsdata->getTrainingen() == $trainingen[$i] && $timestamp < time()) {
                                                    $check = false;
                                                    if(date('m', time()) < '08') {
                                                        if(($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('m') < '08') ||
                                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('m') >= '08')) {
                                                            $check = true;
                                                        } else {
                                                            break;
                                                        }
                                                    } else {
                                                        if($lesdatum->format('Y') == date('Y', time())) {
                                                            if ($lesdatum->format('m') < '08') {
                                                                break;
                                                            } else {
                                                                $check = true;
                                                            }
                                                        }
                                                    }
                                                    if($check) {
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
                                            if($functiePerPersoon->getFunctie() == 'Trainer') {
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
                                    if($toekomst) {
                                        $lesdatum = $trainingsdata[$j]->getLesdatum();
                                        $timestamp = $lesdatum->getTimestamp();
                                        $timestampPlusDag = ((int)$timestamp + 86400);
                                        if($timestampPlusDag > time()) {
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
                                        if($timestamp < time()) {
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
                                if($toekomst) {
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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

    private function getTrainingsdataVoorKruisjeslijst($userObject, $id, $groepId)
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
                            for ($j=0; $j<count($trainingen); $j++) {
                                $trainingsdataVoorKruisjeslijst->trainingen[$j] = new \stdClass();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->dag = $trainingen[$j]->getDag();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdvan();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdtot();
                                $trainingsdataVoorKruisjeslijst->trainingen[$j]->trainingsdata = array();
                                $trainingsdata = $trainingen[$j]->getTrainingsdata();
                                for ($i=(count($trainingsdata)-1); $i>=0; $i--) {
                                    $lesdatum = $trainingsdata[$i]->getLesdatum();
                                    $timestamp = $lesdatum->getTimestamp();
                                    if (($timestamp) > (time()-604800)) {
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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

    private function getTrainingsdatumDetails($userObject, $id, $groepId, $trainingsdatumId)
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
                            for ($j=0; $j<count($trainingen); $j++) {
                                $trainingsdata = $trainingen[$j]->getTrainingsdata();
                                for ($i=(count($trainingsdata)-1); $i>=0; $i--) {
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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

    private function getPersonenVoorTrainingsdatum($trainingsdataObject, $groepId)
    {
        /** @var Trainingsdata $trainingsdataObject */
        /** @var Trainingen $training */
        $training = $trainingsdataObject->getTrainingen();
        $aanwezigheidPersonen = new \stdClass();
        $personen = $training->getPersoon();
        $aanwezigheidPersonen->trainers = array();
        $aanwezigheidPersonen->assistenten = array();
        $aanwezigheidPersonen->turnsters = array();
        for ($i=0; $i<count($personen); $i++) {
            $functies = $personen[$i]->getFunctie();
            foreach($functies as $functie) {
                $groep = $functie->getGroep();
                if($groep->getId() == $groepId) {
                    if($functie->getFunctie() == 'Trainer') {
                        $aanwezigheidPersonen->trainers[$i] = new \stdClass();
                        $aanwezigheidPersonen->trainers[$i]->voornaam = $personen[$i]->getVoornaam();
                        $aanwezigheidPersonen->trainers[$i]->achternaam = $personen[$i]->getAchternaam();
                        $aanwezigheidPersonen->trainers[$i]->id = $personen[$i]->getId();
                    } elseif($functie->getFunctie() == 'Assistent-Trainer') {
                        $aanwezigheidPersonen->assistenten[$i] = new \stdClass();
                        $aanwezigheidPersonen->assistenten[$i]->voornaam = $personen[$i]->getVoornaam();
                        $aanwezigheidPersonen->assistenten[$i]->achternaam = $personen[$i]->getAchternaam();
                        $aanwezigheidPersonen->assistenten[$i]->id = $personen[$i]->getId();
                    } elseif($functie->getFunctie() == 'Turnster') {
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
        for ($i=0; $i<count($aanwezigheid); $i++) {
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
            foreach ($_POST as $key=>$value) {
                if(preg_match("/^afgemeld/", $key)) {
                    $afgemeldMaarAanwezigid = explode("_", $key);
                    $afgemeldMaarAanwezig[] = (int)$afgemeldMaarAanwezigid[1];
                }
                else {
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
                        WHERE aanwezigheid.persoon = :persoon')
                        ->setParameter('persoon', $persoonsId);
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
            $user->addPersoon($persoon);
            $em->persist($persoon);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Inloggegevens website Donar')
                ->setFrom($_SESSION['username'])
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
                    ->setFrom($_SESSION['username'])
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
            $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
            $this->groepItems = $this->wedstrijdLinkItems[0];
            $this->header = $this->getHeader('wedstrijdturnen');
            $this->calendarItems = $this->getCalendarItems();
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
                    if($groep->getId() == $groepId && $functie->getFunctie() == 'Turnster') {
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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

    private function checkGroupAuthorization($userObject, $id, $groepId, array $roles)
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
                            break;
                        }
                    }
                }
            }
        }
        $response['authorized'] = $authorized;
        $response['groep'] = $groep;
        $response['functie'] = $functie->getFunctie();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
            for ($counter=(count($uitslagen)-1); $counter>=0;$counter--) {
                if ($uitslagen[$counter]->getDatum()->format('m')>7) {
                    $wedstrijduitslagen[$uitslagen[$counter]->getDatum()->format('Y')][] = $uitslagen[$counter]->getAll();
                } else {
                    $wedstrijduitslagen[($uitslagen[$counter]->getDatum()->format('Y')-1)][] = $uitslagen[$counter]->getAll();
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
            'wedstrijduitslagen' =>$wedstrijduitslagen,
            'functie' => $functie,
            'groepId' =>$groepId,
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
                'groepId' =>$groepId,
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        if($request->getMethod() == 'POST')
        {
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
            'groepId' =>$groepId,
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
            for($i=0;$i<count($functies);$i++) {
                $persoonEdit->functie[$i] = new \stdClass();
                $persoonEdit->functie[$i]->functie = $functies[$i]->getFunctie();
                $groep = $functies[$i]->getGroep();
                $persoonEdit->functie[$i]->groepNaam = $groep->getName();
                $persoonEdit->functie[$i]->groepId = $groep->getId();
                $trainingen = $groep->getTrainingen();
                $persoonEdit->functie[$i]->trainingen = array();
                for($j=0;$j<count($trainingen);$j++) {
                    $persoonTrainingen = $result->getTrainingen();
                    for($k=0;$k<count($persoonTrainingen);$k++) {
                        if($trainingen[$j]->getId() == $persoonTrainingen[$k]->getId()) {
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
            for($i=0;$i<count($groepen);$i++)
            {
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
                for($j=0;$j<count($trainingen);$j++) {
                    $groepenItems[$i]->trainingen[$j] = new \stdClass();
                    $groepenItems[$i]->trainingen[$j]->dag = $trainingen[$j]->getDag();
                    $groepenItems[$i]->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdVan();
                    $groepenItems[$i]->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdTot();
                    $groepenItems[$i]->trainingen[$j]->id = $trainingen[$j]->getId();
                }
            }
            if($request->getMethod() == 'POST') {
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
                        if(!$check) {
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

    private function getSelectieTurnsterInfo($turnsterId, $groepObject)
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
        $imageSize = getimagesize('http://www.donargym.nl/uploads/selectiefotos/' . $turnster->foto);
        $turnster->width = $imageSize[0];
        $turnster->height = $imageSize[1];
        $trainingen = $persoonObject->getTrainingen();
        $userObject = $persoonObject->getUser();
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
            for ($i=0; $i<count($trainingen); $i++) {
                if (($functie->getGroep() == $trainingen[$i]->getGroep() && $functie->getGroep() == $groepObject)) {
                    $turnster->trainingen[$i] = new \stdClass();
                    $turnster->trainingen[$i]->id = $trainingen[$i]->getId();
                    $turnster->trainingen[$i]->dag = $trainingen[$i]->getDag();
                    $turnster->trainingen[$i]->tijdvan = $trainingen[$i]->getTijdvan();
                    $turnster->trainingen[$i]->tijdtot = $trainingen[$i]->getTijdtot();
                    $turnster->trainingen[$i]->trainingsdata = array();
                    $trainingsdata = $trainingen[$i]->getTrainingsdata();
                    $counter=0;
                    $aantalTrainingen = 0;
                    $aantalAanwezig = 0;
                    $aanwezigheid = $persoonObject->getAanwezigheid();
                    for ($j = (count($trainingsdata) - 4); $j >= 0; $j--) {
                        $lesdatum = $trainingsdata[$j]->getLesdatum();
                        if (strtotime($lesdatum->format('d-m-Y')) <= time()) {
                            for ($k = (count($aanwezigheid) - 1); $k >= 0; $k--) {
                                $check = false;
                                if(date('m', time()) < '08') {
                                    if(($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                        ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')) {
                                        $check = true;
                                    } else {
                                        break;
                                    }
                                } else {
                                    if($lesdatum->format('Y') == date('Y', time())) {
                                        if ($lesdatum->format('m') < '08') {
                                            break;
                                        } else {
                                            $check = true;
                                        }
                                    }
                                }
                                if($check) {
                                    if ($aanwezigheid[$k]->getTrainingsdata() == $trainingsdata[$j]) {
                                        $aantalTrainingen++;
                                        if($counter < 7) {
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
                        ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')) {
                        $check = true;
                    } else {
                        break;
                    }
                } else {
                    if($lesdatum->format('Y') == date('Y', time())) {
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
        $turnster->percentageKleur = $this->colorGenerator($turnster->percentageAanwezig);
        $turnster->aantalAanwezig = $aantalAanwezig;
        $turnster->aantalTrainingen = $aantalTrainingen;
        return $turnster;
    }

    /**
     * @Security("has_role('ROLE_ASSISTENT')")
     * @Route("/inloggen/selectie/{persoonId}/viewTurnster/{turnsterId}/{groepId}/", name="viewSelectieTurnster")
     * @Method({"GET"})
     */
    public
    function viewSelectieTurnster($persoonId, $turnsterId, $groepId)
    {
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        }
        return $this->render('inloggen/selectieViewTurnster.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
            'persoon' => $persoon,
            'user' => $user,
            'persoonItems' => $persoonItems,
            'functie' => $functie,
            'groepId' =>$groepId,
            'turnster' => $turnster,
        ));
    }

    private function getVloermuziekjes($groepObject)
    {
        /** @var Groepen $groepObject */
        $vloermuziek = array();
        $personen = $groepObject->getPeople();
        for ($i=0; $i<count($personen); $i++) {
            $functies = $personen[$i]->getFunctie();
            foreach ($functies as $functie) {
                if($functie->getFunctie() == 'Turnster' && $functie->getGroep() == $groepObject) {
                    $persoonInfo = $personen[$i]->getAll();
                    if ($persoonInfo->categorie == 'Jeugd 2' || $persoonInfo->categorie == 'Junior' || $persoonInfo->categorie == 'Senior')
                    {
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader('wedstrijdturnen');
        $this->calendarItems = $this->getCalendarItems();
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
                    if(in_array(strtolower($vloermuziek->getFile()->getClientOriginalExtension()), $extensions)) {
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
                    } else {$error = 'Please upload a valid audio file: mp3 or wma';}
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
                ));

            }
        }
    }
}