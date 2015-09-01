<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FotoUpload;
use AppBundle\Entity\Functie;
use AppBundle\Entity\Groepen;
use AppBundle\Entity\Persoon;
use AppBundle\Entity\Trainingen;
use AppBundle\Form\Type\ContactgegevensType;
use AppBundle\Form\Type\Email1Type;
use AppBundle\Form\Type\Email2Type;
use AppBundle\Form\Type\UserType;
use Doctrine\ORM\EntityManager;
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
    protected $header;
    protected $calendarItems;

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
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $userObject = $this->getUser();
        $user = $this->getBasisUserGegevens($userObject);
        $persoon = $this->getBasisPersoonsGegevens($userObject);
        return $this->render('inloggen/selectieIndexPage.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'persoon' => $persoon,
            'user' => $user,
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
        return($user);
    }

    private function getBasisPersoonsGegevens($userObject)
    {
        $personen = $userObject->getPersoon();
        $persoon = array();
        for ($i=0;$i<count($personen);$i++) {
            $persoon[$i] = new \stdClass();
            $persoon[$i]->voornaam = $personen[$i]->getVoornaam();
            $persoon[$i]->achternaam = $personen[$i]->getAchternaam();
            $persoon[$i]->geboortedatum = $personen[$i]->getGeboortedatum();
            $persoon[$i]->id = $personen[$i]->getId();
            /** @var SelectieFoto $foto */
            $foto = $personen[$i]->getFoto();
            if(count($foto) > 0) {
                $persoon[$i]->foto = $foto->getLocatie();
            }
            else {
                $persoon[$i]->foto = 'uploads/selectiefotos/plaatje.jpg';
            }
            $groepen = $personen[$i]->getGroepen();
            $persoon[$i]->groepen = array();
            for ($j=0;$j<count($groepen);$j++) {
                $persoon[$i]->groepen[$j] = new \stdClass();
                $persoon[$i]->groepen[$j]->naam = $groepen[$j]->getName();
            }
            $trainerFunctie = false;
            $functies = $personen[$i]->getFunctie();
            foreach ($functies as $functie) {
                if($functie->getFunctie() != 'Turnster') {
                    $trainerFunctie = true;
                }
            } if($trainerFunctie) {
                $persoon[$i]->functie = 'Trainer';
            }

        }
        return($persoon);
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/editContactgegevens/", name="editContactgegevens")
     * @Method({"GET", "POST"})
     */
    public function editContactgegevens(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
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
        }
        else {
            return $this->render('inloggen/editContactgegevens.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'persoon' => $persoon,
                'user' => $user,
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
        $this->header = 'bannerhome'.rand(1,2);
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
        }
        else {
            return $this->render('inloggen/editEmail.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'persoon' => $persoon,
                'user' => $user,
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
        $this->header = 'bannerhome'.rand(1,2);
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
        }
        else {
            return $this->render('inloggen/editEmail2.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'persoon' => $persoon,
                'user' => $user,
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
            if($request->request->get('pass1') != $request->request->get('pass2')) {
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
        $this->header = 'bannerhome'.rand(1,2);
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
        ));
    }

    private function getOnePersoon($userObject, $id)
    {
        $personen = $userObject->getPersoon();
        foreach ($personen as $persoon) {
            /** @var Persoon $persoon */
            if($persoon->getId() == $id) {
                $persoonItems = new \stdClass();
                $persoonItems->id = $persoon->getId();
                $persoonItems->voornaam = $persoon->getVoornaam();
                $persoonItems->achternaam = $persoon->getAchternaam();
                $foto = $persoon->getFoto();
                if ($foto == null) {$persoonItems->foto = "plaatje.jpg";}
                else {$persoonItems->foto = $foto->getLocatie();}
                $persoonItems->geboortedatum = $persoon->getGeboortedatum();
                $persoonItems->categorie = $persoon->categorie(strtotime($persoonItems->geboortedatum));
                // TODO: functie, (+ groepen), stukje, aanwezigheid, doelen, trainingen
            }
        }
        return($persoonItems);
    }

    /**
     * @Security("has_role('ROLE_TURNSTER')")
     * @Route("/inloggen/selectie/{id}/", name="showPersoon")
     * @Method({"GET"})
     */
    public function showPersoon($id)
    {
        $this->header = 'bannerhome'.rand(1,2);
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
        ));
    }

}