<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FotoUpload;
use AppBundle\Entity\Functie;
use AppBundle\Entity\Groepen;
use AppBundle\Entity\Persoon;
use AppBundle\Entity\Trainingen;
use AppBundle\Form\Type\UserType;
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


/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * @Route("/admin/", name="getAdminIndexPage")
     * @Method("GET")
     */
    public function getIndexPageAction()
    {
        $this->setBasicPageData();
        return $this->render('inloggen/adminIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Route("/admin/foto/", name="getAdminFotoPage")
     * @Method("GET")
     */
    public function getAdminFotoPage()
    {
        $this->setBasicPageData();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT fotoupload
                FROM AppBundle:FotoUpload fotoupload
                ORDER BY fotoupload.naam');
        $content = $query->getResult();
        $contentItems = array();
        for($i=0;$i<count($content);$i++)
        {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render('inloggen/adminFotos.html.twig', array(
            'contentItems' => $contentItems,
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Template()
     * @Route("/admin/foto/add/", name="addAdminFotoPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminFotoPageAction(Request $request)
    {
        $this->setBasicPageData();
        $foto = new FotoUpload();
        $form = $this->createFormBuilder($foto)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($foto);
            $em->flush();
            $this->get('helper.imageresizer')->resizeImage($foto->getAbsolutePath(), $foto->getUploadRootDir()."/" , null, $width=597);
            return $this->redirectToRoute('getAdminFotoPage');
        }
        else {
            return $this->render('inloggen/addAdminFotos.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/admin/foto/remove/{id}/", name="removeAdminFotoPage")
     * @Method({"GET", "POST"})
     */
    public function removeAdminFotoPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->setBasicPageData();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fotoupload
                FROM AppBundle:FotoUpload fotoupload
                WHERE fotoupload.id = :id')
                ->setParameter('id', $id);
            $foto = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($foto) > 0)
            {
                return $this->render('inloggen/removeAdminFotos.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $foto->getAll(),
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
        }
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fotoupload
                FROM AppBundle:FotoUpload fotoupload
                WHERE fotoupload.id = :id')
                ->setParameter('id', $id);
            $foto = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($foto);
            $em->flush();
            return $this->redirectToRoute('getAdminFotoPage');
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/admin/bestanden/", name="getAdminBestandenPage")
     * @Method("GET")
     */
    public function getAdminBestandenPage()
    {
        $this->setBasicPageData();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT fileupload
                FROM AppBundle:FileUpload fileupload
                ORDER BY fileupload.naam');
        $content = $query->getResult();
        $contentItems = array();
        for($i=0;$i<count($content);$i++)
        {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render('inloggen/adminUploads.html.twig', array(
            'contentItems' => $contentItems,
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Template()
     * @Route("/admin/bestanden/add/", name="addAdminBestandenPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminBestandenPageAction(Request $request)
    {
        $this->setBasicPageData();
        $file = new FileUpload();
        $form = $this->createFormBuilder($file)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($file);
            $em->flush();
            return $this->redirectToRoute('getAdminBestandenPage');
        }
        else {
            return $this->render('inloggen/addAdminUploads.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'form' => $form->createView(),
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/admin/bestanden/remove/{id}/", name="removeAdminBestandenPage")
     * @Method({"GET", "POST"})
     */
    public function removeAdminBestandenPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->setBasicPageData();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fileupload
                FROM AppBundle:FileUpload fileupload
                WHERE fileupload.id = :id')
                ->setParameter('id', $id);
            $file = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($file) > 0)
            {
                return $this->render('inloggen/removeAdminUploads.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'content' => $file->getAll(),
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
        }
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fileupload
                FROM AppBundle:FileUpload fileupload
                WHERE fileupload.id = :id')
                ->setParameter('id', $id);
            $file = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($file);
            $em->flush();
            return $this->redirectToRoute('getAdminBestandenPage');
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }

    /**
     * @Route("/admin/selectie/", name="getAdminSelectiePage")
     * @Method("GET")
     */
    public function getAdminSelectiePage()
    {
        $this->setBasicPageData();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT functie
                FROM AppBundle:Functie functie
                WHERE functie.functie = :functie
                OR functie.functie = :functie2')
            ->setParameter('functie', 'Trainer')
            ->setParameter('functie2', 'Assistent-Trainer');
        /** @var Functie $functies */
        $functies = $query->getResult();
        $persoonItems = array();
        $ids = array();
        for($i=0;$i<count($functies);$i++)
        {
            $persoon = $functies[$i]->getPersoon();
            if(!(in_array($persoon->getId(), $ids)))
            {
                $ids[] = $persoon->getId();
                $persoonItems[$i] = new \stdClass();
                $persoonItems[$i]->id = $persoon->getId();
                $persoonItems[$i]->voornaam = $persoon->getVoornaam();
                $persoonItems[$i]->achternaam = $persoon->getAchternaam();
            }
        }
        return $this->render('inloggen/adminSelectie.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'personen' => $persoonItems,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Route("/admin/selectie/add/", name="addAdminTrainerPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminTrainerPageAction(Request $request)
    {
        $this->setBasicPageData();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT groepen
                FROM AppBundle:Groepen groepen');
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
            $role = 'ROLE_ASSISTENT';
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
                if ($this->get('request')->request->get('groep_' . $groep->getId()) == 'Trainer' || $this->get('request')->request->get('groep_' . $groep->getId()) == 'Assistent-Trainer') {
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
            }
            else{
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
                            'email2' =>$user->getEmail2(),
                            'password' => $password
                        )
                    ),
                    'text/plain'
                );
            $this->get('mailer')->send($message);

            if($user->getEmail2())
            {
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
                                'email2' =>$user->getEmail2(),
                                'password' => $password
                            )
                        ),
                        'text/plain'
                    );
                $this->get('mailer')->send($message);
            }
            return $this->redirectToRoute('getAdminSelectiePage');
        }
        return $this->render('inloggen/adminAddTrainer.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'groepen' => $groepenItems,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Route("/admin/selectie/edit/{id}/", name="editAdminTrainerPage")
     * @Method({"GET", "POST"})
     */
    public function editAdminTrainerPageAction(Request $request, $id)
    {
        $this->setBasicPageData();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
            ->setParameter('id', $id);
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
                ->setParameter('id', $id);

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
                if (!($this->get('request')->request->get('groep_' . $groep->getId()) == 'Trainer' || $this->get('request')->request->get('groep_' . $groep->getId()) == 'Assistent-Trainer')) {
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
                if ($this->get('request')->request->get('groep_' . $groep->getId()) == 'Trainer' ||
                    $this->get('request')->request->get('groep_' . $groep->getId()) == 'Assistent-Trainer') {
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
            $role = 'ROLE_TURNSTER';
            $personen = $user->getPersoon();
            foreach($personen as $persoonItem) {
                $functie = $persoonItem->getFunctie();
                if(count($functie)>0) {
                    foreach ($functie as $functieItem) {
                        if($functieItem->getFunctie() == 'Trainer') {
                            $role = 'ROLE_TRAINER';
                        }
                        elseif($functieItem->getFunctie() == 'Assistent-Trainer' && $role == 'ROLE_TURNSTER') {
                            $role = 'ROLE_ASSISTENT';
                        }
                    }
                }
            }
            $user->setRole($role);

            $em->persist($persoon);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('getAdminSelectiePage');
        }
        return $this->render('inloggen/adminEditTrainer.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'groepen' => $groepenItems,
            'persoonEdit' => $persoonEdit,
            'wedstrijdLinkItems' => $this->groepItems,
        ));
    }

    /**
     * @Route("/admin/selectie/remove/{id}/", name="removeAdminTrainerPage")
     * @Method({"GET", "POST"})
     */
    public function removeAdminTrainerPage($id, Request $request)
    {
        if($request->getMethod() == 'GET')
        {
            $this->setBasicPageData();
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
                ->setParameter('id', $id);
            $persoon = $query->setMaxResults(1)->getOneOrNullResult();
            if(count($persoon) > 0)
            {
                return $this->render('inloggen/adminRemoveTrainer.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'voornaam' => $persoon->getVoornaam(),
                    'achternaam' => $persoon->getAchternaam(),
                    'id' => $persoon->getId(),
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                ));
            }
        }
        elseif($request->getMethod() == 'POST')
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
                ->setParameter('id', $id);
            $persoon = $query->setMaxResults(1)->getOneOrNullResult();
            $user = $persoon->getUser();
            $personen = $user->getPersoon();
            $em->remove($persoon);
            $em->flush();
            $role = 'ROLE_TURNSTER';
            if(count($personen) == 0) {
                $em->remove($user);
                $em->flush();
            }
            else {
                foreach($personen as $persoonItem) {
                    $functie = $persoonItem->getFunctie();
                    foreach ($functie as $functieItem) {
                        if($functieItem->getFunctie() == 'Trainer') {
                            $role = 'ROLE_TRAINER';
                        }
                        elseif($functieItem->getFunctie() == 'Assistent-Trainer' && $role == 'ROLE_TURNSTER') {
                            $role = 'ROLE_ASSISTENT';
                        }
                    }
                }
                $user->setRole($role);
                $em->flush();
            }
            return $this->redirectToRoute('getAdminSelectiePage');
        }
        else
        {
            return $this->render('error/pageNotFound.html.twig', array(
                'calendarItems' => $this->calendarItems,
                'header' => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            ));
        }
    }
}