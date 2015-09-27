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
use AppBundle\Form\Type\ContactgegevensType;
use AppBundle\Form\Type\Email1Type;
use AppBundle\Form\Type\Email2Type;
use AppBundle\Form\Type\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Func;
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

                    $aantalAanwezig = 0;
                    $aantalTrainingen = 0;
                    $totaalAanwezigheid = $persoon->getAanwezigheid();
                    for($counter=(count($totaalAanwezigheid)-1);$counter >= 0;$counter--) {
                        $check = false;
                        /** @var Trainingsdata $trainingsdatum */
                        $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
                        $lesdatum = $trainingsdatum->getLesdatum();
                        /** @var Trainingen $training */
                        $training = $trainingsdatum->getTrainingen();
                        /** @var Groepen $trainingGroep */
                        $trainingGroep = $training->getGroep();
                        if($trainingGroep->getId() == $persoonItems->functies[$i]->groepId) {
                            if (date('m', time()) < '08') {
                                if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                    ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                ) {
                                    $check = true;
                                } else {
                                    $counter = 0;
                                }
                            } else {
                                if ($lesdatum->format('Y') != date('Y', time())) {
                                    $counter = 0;
                                } else {
                                    $check = true;
                                }
                            }
                        }
                        if($check) {
                            $aantalTrainingen++;
                            if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                                $aantalAanwezig++;
                            }
                        }
                    }
                    if($aantalTrainingen != 0) {
                        $persoonItems->functies[$i]->percentageAanwezig = 100*$aantalAanwezig/$aantalTrainingen;
                    } else {
                        $persoonItems->functies[$i]->percentageAanwezig = 100;
                    }
                    $persoonItems->functies[$i]->percentageKleur = $this->colorGenerator($persoonItems->functies[$i]->percentageAanwezig);

                    $groepFuncties = $groep->getFuncties();
                    for ($j = 0; $j < count($groepFuncties); $j++) {
                        if ($groepFuncties[$j]->getFunctie() == 'Turnster') {
                            $persoonItems->functies[$i]->turnster[$j] = new \stdClass();
                            /** @var Persoon $turnster */
                            $turnster = $groepFuncties[$j]->getPersoon();

                            $aantalAanwezig = 0;
                            $aantalTrainingen = 0;
                            $totaalAanwezigheid = $turnster->getAanwezigheid();
                            for($counter=(count($totaalAanwezigheid)-1);$counter >= 0;$counter--) {
                                $check = false;
                                /** @var Trainingsdata $trainingsdatum */
                                $trainingsdatum = $totaalAanwezigheid[$counter]->getTrainingsdata();
                                $lesdatum = $trainingsdatum->getLesdatum();
                                /** @var Trainingen $training */
                                $training = $trainingsdatum->getTrainingen();
                                /** @var Groepen $trainingGroep */
                                $trainingGroep = $training->getGroep();
                                if($trainingGroep->getId() == $persoonItems->functies[$i]->groepId) {
                                    if (date('m', time()) < '08') {
                                        if (($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')
                                        ) {
                                            $check = true;
                                        } else {
                                            $counter = 0;
                                        }
                                    } else {
                                        if ($lesdatum->format('Y') != date('Y', time())) {
                                            $counter = 0;
                                        } else {
                                            $check = true;
                                        }
                                    }
                                }
                                if($check) {
                                    $aantalTrainingen++;
                                    if (strtolower($totaalAanwezigheid[$counter]->getAanwezig()) == 'x') {
                                        $aantalAanwezig++;
                                    }
                                }
                            }
                            if($aantalTrainingen != 0) {
                                $persoonItems->functies[$i]->turnster[$j]->percentageAanwezig = 100*$aantalAanwezig/$aantalTrainingen;
                            } else {
                                $persoonItems->functies[$i]->turnster[$j]->percentageAanwezig = 100;
                            }
                            $persoonItems->functies[$i]->turnster[$j]->percentageKleur = $this->colorGenerator($persoonItems->functies[$i]->turnster[$j]->percentageAanwezig);

                            $persoonItems->functies[$i]->turnster[$j]->voornaam = $turnster->getVoornaam();
                            $persoonItems->functies[$i]->turnster[$j]->achternaam = $turnster->getAchternaam();
                            $persoonItems->functies[$i]->turnster[$j]->id = $turnster->getId();
                            $geboortedatum = $turnster->getGeboortedatum();
                            $persoonItems->functies[$i]->turnster[$j]->geboortedatum = date('d-m-Y', strtotime($geboortedatum));
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
                        //TODO: Als al afgemeld, dit weergeven (alle aanwezigheid turnster uit toekomst ophalen
                        //TODO: Formulier van view maken
                        //TODO: Verplicht reden opgeven
                        //TODO: Verwerking POST
                        //TODO: Toevoegen aan turnster aanwezigheid
                        //TODO: checkboxes checked if foutmelding
                        //TODO: Mail sturen na afmelding
                        $counter = 0;
                        for ($j = (count($trainingsdata) - 1); $j >= 0; $j--) {
                            $lesdatum = $trainingsdata[$j]->getLesdatum();
                            if (strtotime($lesdatum->format('d-m-Y')) >= time()) {
                                $persoonItems->trainingen[$i]->trainingsdata[$j] = new \stdClass();
                                $persoonItems->trainingen[$i]->trainingsdata[$j]->id = $trainingsdata[$j]->getId();
                                $persoonItems->trainingen[$i]->trainingsdata[$j]->lesdatum = $lesdatum->format('d-m-Y');
                                $counter++;
                                if ($counter == 9) {
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
                        for ($j = (count($trainingsdata) - 1); $j >= 0; $j--) {
                            $lesdatum = $trainingsdata[$j]->getLesdatum();
                            if (strtotime($lesdatum->format('d-m-Y')) <= time()) {
                                for ($k = (count($aanwezigheid) - 1); $k >= 0; $k--) {
                                    $check = false;
                                    if(date('m', time()) < '08') {
                                        if(($lesdatum->format('Y') == date('Y', time()) && $lesdatum->format('Y') < '08') ||
                                            ($lesdatum->format('Y') == (date('Y', time()) - 1) && $lesdatum->format('Y') >= '08')) {
                                            $check = true;
                                        } else {
                                            $k=0;
                                        }
                                    } else {
                                        if($lesdatum->format('Y') != date('Y', time())) {
                                            $counter=0;
                                        } else {
                                            $check = true;
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
                        $persoonItems->trainingen[$i]->percentageKleur = $this->colorGenerator($persoonItems->trainingen[$i]->percentageAanwezig);
                    }
                    // TODO: turnsters, aanwezigheid, doelen
                }
            }
        }
        /*var_dump($persoonItems);
        die;*/
        return ($persoonItems);
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
    function Afmelding($id, $groepId)
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
     * @Route("/inloggen/selectie/{trainerId}/remove/{turnsterId}", name="removeSelectieTurnsterPage")
     * @Method({"GET", "POST"})
     */
    public
    function removeSelectieTurnsterPage($trainerId, $turnsterId, Request $request)
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
            $user = $persoon->getUser();
            $personen = $user->getPersoon();
            $em->remove($persoon);
            $em->flush();
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

    //todo: edit turnster functie en view
}