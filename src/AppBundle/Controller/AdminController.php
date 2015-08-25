<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\FotoUpload;
use AppBundle\Entity\Functie;
use AppBundle\Entity\Groepen;
use AppBundle\Entity\Persoon;
use AppBundle\Entity\Trainingen;
use AppBundle\Form\Type\UserType;
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
    protected $header;
    protected $calendarItems;

    public function __construct()
    {
    }

    /**
     * @Route("/admin/", name="getAdminIndexPage")
     * @Method("GET")
     */
    public function getIndexPageAction()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        return $this->render('inloggen/adminIndex.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header
        ));
    }

    /**
     * @Route("/admin/foto/", name="getAdminFotoPage")
     * @Method("GET")
     */
    public function getAdminFotoPage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
            'header' => $this->header
        ));
    }

    /**
     * @Template()
     * @Route("/admin/foto/add/", name="addAdminFotoPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminFotoPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
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
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
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
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/admin/bestanden/", name="getAdminBestandenPage")
     * @Method("GET")
     */
    public function getAdminBestandenPage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
            'header' => $this->header
        ));
    }

    /**
     * @Template()
     * @Route("/admin/bestanden/add/", name="addAdminBestandenPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminBestandenPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
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
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
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
                'header' => $this->header
            ));
        }
    }

    /**
     * @Route("/admin/selectie/", name="getAdminSelectiePage")
     * @Method("GET")
     */
    public function getAdminSelectiePage()
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
                FROM AppBundle:Persoon persoon');
        $personen = $query->getResult();
        $persoonItems = array();
        for($i=0;$i<count($personen);$i++)
        {
            $persoonItems[$i] = new \stdClass();
            $persoonItems[$i]->id = $personen[$i]->getId();
            $persoonItems[$i]->voornaam = $personen[$i]->getVoornaam();
            $persoonItems[$i]->achternaam = $personen[$i]->getAchternaam();
        }
        return $this->render('inloggen/adminSelectie.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'personen' => $persoonItems
        ));
    }

    /**
     * @Route("/admin/selectie/add/", name="addAdminTrainerPage")
     * @Method({"GET", "POST"})
     */
    public function addAdminTrainerPageAction(Request $request)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
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
            try{$this->get('mailer')->send($message);}
            catch(\Exception $e){
                var_dump($e->getMessage());die;
            }

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
                try{$this->get('mailer')->send($message);}
                catch(\Exception $e){
                    var_dump($e->getMessage());die;
                }
            }

            return $this->redirectToRoute('getAdminSelectiePage');
        }
        return $this->render('inloggen/adminAddTrainer.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'groepen' => $groepenItems
        ));
    }

    /**
     * @Route("/admin/selectie/edit/{id}/", name="editAdminTrainerPage")
     * @Method({"GET", "POST"})
     */
    public function editAdminTrainerPageAction(Request $request, $id)
    {
        $this->header = 'bannerhome'.rand(1,2);
        $this->calendarItems = $this->getCalendarItems();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT persoon
                FROM AppBundle:Persoon persoon
                WHERE persoon.id = :id')
            ->setParameter('id', $id);
        $result = $query->setMaxResults(1)->getOneOrNullResult();
        $persoon = new \stdClass();
        $persoon->voornaam = $result->getVoornaam();
        $persoon->achternaam = $result->getAchternaam();
        $persoon->geboortedatum = $result->getGeboortedatum();
        $user = $result->getUser();
        $persoon->username = $user->getUsername();
        $persoon->email2 = $user->getEmail2();
        $persoon->straatnr = $user->getStraatnr();
        $persoon->postcode = $user->getPostcode();
        $persoon->plaats = $user->getPlaats();
        $persoon->tel1 = $user->getTel1();
        $persoon->tel2 = $user->getTel2();
        $persoon->tel3 = $user->getTel3();
        $functies = $result->getFunctie();
        $persoon->functie = array();
        for($i=0;$i<count($functies);$i++) {
            $persoon->functie[$i] = new \stdClass();
            $persoon->functie[$i]->functie = $functies[$i]->getFunctie();
            $groep = $functies[$i]->getGroep();
            $persoon->functie[$i]->groepNaam = $groep->getName();
            $persoon->functie[$i]->groepId = $groep->getId();
            $trainingen = $groep->getTrainingen();
            $persoon->functie[$i]->trainingen = array();
            for($j=0;$j<count($trainingen);$j++) {
                $persoonTrainingen = $result->getTrainingen();
                for($k=0;$k<count($persoonTrainingen);$k++) {
                    if($trainingen[$j]->getId() == $persoonTrainingen[$k]->getId()) {
                        $persoon->functie[$i]->trainingen[$k] = new \stdClass();
                        $persoon->functie[$i]->trainingen[$k]->trainingId = $persoonTrainingen[$k]->getId();
                    }
                }
            }
        }

        //@todo: laten zien in front wat al geselecteerd is
        //@todo: bij post eerst alles updaten, daarna role checken en evt. aanpassen

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
            try{$this->get('mailer')->send($message);}
            catch(\Exception $e){
                var_dump($e->getMessage());die;
            }

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
                try{$this->get('mailer')->send($message);}
                catch(\Exception $e){
                    var_dump($e->getMessage());die;
                }
            }

            return $this->redirectToRoute('getAdminSelectiePage');
        }
        return $this->render('inloggen/adminEditTrainer.html.twig', array(
            'calendarItems' => $this->calendarItems,
            'header' => $this->header,
            'groepen' => $groepenItems,
            'persoon' => $persoon
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
            $this->header = 'bannerhome'.rand(1,2);
            $this->calendarItems = $this->getCalendarItems();
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
                ));
            }
            else
            {
                return $this->render('error/pageNotFound.html.twig', array(
                    'calendarItems' => $this->calendarItems,
                    'header' => $this->header
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
                    if($functie[0]->getFunctie() == 'Trainer') {
                        $role = 'ROLE_TRAINER';
                    }
                    elseif($functie[0]->getFunctie() == 'Assistent-Trainer' && $role == 'ROLE_TURNSTER') {
                        $role = 'ROLE_ASSISTENT';
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
                'header' => $this->header
            ));
        }
    }
}