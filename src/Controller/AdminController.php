<?php

namespace App\Controller;

use App\Entity\FileUpload;
use App\Entity\FotoUpload;
use App\Entity\Functie;
use App\Entity\Groepen;
use App\Entity\Persoon;
use App\Entity\Scores;
use App\Entity\ToegestaneNiveaus;
use App\Entity\Trainingen;
use App\Entity\Turnster;
use App\Entity\User;
use App\Entity\Vereniging;
use App\Helper\ImageResizer;
use App\Repository\ScoresRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends BaseController
{
    /**
     * @Route("/admin/", name="getAdminIndexPage", methods={"GET"})
     */
    public function getIndexPageAction()
    {
        $this->setBasicPageData();
        return $this->render(
            'inloggen/adminIndex.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/foto/", name="getAdminFotoPage", methods={"GET"})
     */
    public function getAdminFotoPage()
    {
        $this->setBasicPageData();
        $em           = $this->getDoctrine()->getManager();
        $query        = $em->createQuery(
            'SELECT fotoupload
                FROM App:FotoUpload fotoupload
                ORDER BY fotoupload.naam'
        );
        $content      = $query->getResult();
        $contentItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'inloggen/adminFotos.html.twig',
            array(
                'contentItems'       => $contentItems,
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/foto/add/", name="addAdminFotoPage", methods={"GET", "POST"})
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
            $imageResizer = new ImageResizer();
            $imageResizer->resizeImage(
                $foto->getAbsolutePath(),
                $foto->getUploadRootDir() . "/",
                null,
                $width = 597
            );
            return $this->redirectToRoute('getAdminFotoPage');
        } else {
            return $this->render(
                'inloggen/addAdminFotos.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/admin/foto/remove/{id}/", name="removeAdminFotoPage", methods={"GET", "POST"})
     */
    public function removeAdminFotoPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fotoupload
                FROM App:FotoUpload fotoupload
                WHERE fotoupload.id = :id'
            )
                ->setParameter('id', $id);
            $foto  = $query->setMaxResults(1)->getOneOrNullResult();
            if ($foto) {
                return $this->render(
                    'inloggen/removeAdminFotos.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'content'            => $foto->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fotoupload
                FROM App:FotoUpload fotoupload
                WHERE fotoupload.id = :id'
            )
                ->setParameter('id', $id);
            $foto  = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($foto);
            $em->flush();
            return $this->redirectToRoute('getAdminFotoPage');
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/admin/bestanden/", name="getAdminBestandenPage", methods={"GET"})
     */
    public function getAdminBestandenPage()
    {
        $this->setBasicPageData();
        $em           = $this->getDoctrine()->getManager();
        $query        = $em->createQuery(
            'SELECT fileupload
                FROM App:FileUpload fileupload
                ORDER BY fileupload.naam'
        );
        $content      = $query->getResult();
        $contentItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $contentItems[$i] = $content[$i]->getAll();
        }
        return $this->render(
            'inloggen/adminUploads.html.twig',
            array(
                'contentItems'       => $contentItems,
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/bestanden/add/", name="addAdminBestandenPage", methods={"GET", "POST"})
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
        } else {
            return $this->render(
                'inloggen/addAdminUploads.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/admin/bestanden/remove/{id}/", name="removeAdminBestandenPage", methods={"GET", "POST"})
     */
    public function removeAdminBestandenPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fileupload
                FROM App:FileUpload fileupload
                WHERE fileupload.id = :id'
            )
                ->setParameter('id', $id);
            $file  = $query->setMaxResults(1)->getOneOrNullResult();
            if ($file) {
                return $this->render(
                    'inloggen/removeAdminUploads.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'content'            => $file->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT fileupload
                FROM App:FileUpload fileupload
                WHERE fileupload.id = :id'
            )
                ->setParameter('id', $id);
            $file  = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($file);
            $em->flush();
            return $this->redirectToRoute('getAdminBestandenPage');
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/admin/selectie/", name="getAdminSelectiePage", methods={"GET"})
     */
    public function getAdminSelectiePage()
    {
        $this->setBasicPageData();
        $em    = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT functie
                FROM App:Functie functie
                WHERE functie.functie = :functie
                OR functie.functie = :functie2'
        )
            ->setParameter('functie', 'Trainer')
            ->setParameter('functie2', 'Assistent-Trainer');
        /** @var Functie $functies */
        $functies     = $query->getResult();
        $persoonItems = array();
        $ids          = array();
        for ($i = 0; $i < count($functies); $i++) {
            $persoon = $functies[$i]->getPersoon();
            if (!(in_array($persoon->getId(), $ids))) {
                $ids[]                        = $persoon->getId();
                $persoonItems[$i]             = new \stdClass();
                $persoonItems[$i]->id         = $persoon->getId();
                $persoonItems[$i]->voornaam   = $persoon->getVoornaam();
                $persoonItems[$i]->achternaam = $persoon->getAchternaam();
                $persoonItems[$i]->username   = $persoon->getUser()->getUsername();
            }
        }
        return $this->render(
            'inloggen/adminSelectie.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'personen'           => $persoonItems,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/selectie/add/", name="addAdminTrainerPage", methods={"GET", "POST"})
     */
    public function addAdminTrainerPageAction(Request $request)
    {
        $this->setBasicPageData();
        $em           = $this->getDoctrine()->getManager();
        $query        = $em->createQuery(
            'SELECT groepen
                FROM App:Groepen groepen'
        );
        $groepen      = $query->getResult();
        $groepenItems = array();
        for ($i = 0; $i < count($groepen); $i++) {
            $groepenItems[$i]             = new \stdClass();
            $groepenItems[$i]->id         = $groepen[$i]->getId();
            $groepenItems[$i]->naam       = $groepen[$i]->getName();
            $groepenItems[$i]->trainingen = array();
            $query                        = $em->createQuery(
                'SELECT trainingen
                FROM App:Trainingen trainingen
                WHERE trainingen.groep = :id'
            )
                ->setParameter('id', $groepen[$i]->getId());
            $trainingen                   = $query->getResult();
            for ($j = 0; $j < count($trainingen); $j++) {
                $groepenItems[$i]->trainingen[$j]          = new \stdClass();
                $groepenItems[$i]->trainingen[$j]->dag     = $trainingen[$j]->getDag();
                $groepenItems[$i]->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdVan();
                $groepenItems[$i]->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdTot();
                $groepenItems[$i]->trainingen[$j]->id      = $trainingen[$j]->getId();
            }
        }
        if ($request->getMethod() == 'POST') {
            $role  = 'ROLE_ASSISTENT';
            $query = $em->createQuery(
                'SELECT user
                FROM App:User user
                WHERE user.username = :email
                OR user.email2 = :email
				OR user.email3 = :email
				'
            )
                ->setParameter('email', $request->request->get('username'));
            $user  = $query->setMaxResults(1)->getOneOrNullResult();
            if (!$user) {
                $query = $em->createQuery(
                    'SELECT user
                FROM App:User user
                WHERE user.username = :email
                OR user.email2 = :email
				OR user.email3 = :email
					'
                )
                    ->setParameter('email', $request->request->get('email2'));
                $user  = $query->setMaxResults(1)->getOneOrNullResult();
            }
            if (!$user) {
                $query = $em->createQuery(
                    'SELECT user
                FROM App:User user
                WHERE user.username = :email
                OR user.email2 = :email
                OR user.email3 = :email'
                )
                    ->setParameter('email', $request->request->get('email3'));
                $user  = $query->setMaxResults(1)->getOneOrNullResult();
            }


            if ($user) {
                $role    = $user->getRole();
                $newuser = false;
            } else {
                $user    = new \App\Entity\User();
                $newuser = true;
            }
            $persoon = new Persoon();

            $k           = 0;
            $postGroepen = array();
            foreach ($groepen as $groep) {
                if ($request->request->get('groep_' . $groep->getId()) == 'Trainer' || $request->request->get(
                        'groep_' . $groep->getId()
                    ) == 'Assistent-Trainer') {
                    if ($request->request->get('groep_' . $groep->getId()) == 'Trainer') {
                        $role = 'ROLE_TRAINER';
                    } elseif ($request->request->get(
                            'groep_' . $groep->getId()
                        ) == 'Assistent-Trainer' && $role != 'ROLE_TRAINER') {
                        $role = 'ROLE_ASSISTENT';
                    }
                    $query           = $em->createQuery(
                        'SELECT groepen
                        FROM App:Groepen groepen
                        WHERE groepen.id = :id'
                    )
                        ->setParameter('id', $groep->getId());
                    $result          = $query->setMaxResults(1)->getOneOrNullResult();
                    $postGroepen[$k] = $result;
                    $functie         = new Functie();
                    $functie->setFunctie($request->request->get('groep_' . $groep->getId()));
                    $postGroepen[$k]->addFunctie($functie);
                    $persoon->addFunctie($functie);
                    $query      = $em->createQuery(
                        'SELECT trainingen
                        FROM App:Trainingen trainingen
                        WHERE trainingen.groep = :id'
                    )
                        ->setParameter('id', $groep->getId());
                    $trainingen = $query->getResult();
                    foreach ($trainingen as $training) {
                        if ($request->request->get('trainingen_' . $training->getId()) == 'on') {
                            $query  = $em->createQuery(
                                'SELECT trainingen
                                FROM App:Trainingen trainingen
                                WHERE trainingen.id = :id'
                            )
                                ->setParameter('id', $training->getId());
                            $result = $query->setMaxResults(1)->getOneOrNullResult();
                            $persoon->addTrainingen($result);
                        }
                    }
                }
            }
            $persoon->setVoornaam($request->request->get('voornaam'));
            $persoon->setAchternaam($request->request->get('achternaam'));
            $persoon->setGeboortedatum($request->request->get('geboortedatum'));
            $user->setRole($role);
            $user->setUsername($request->request->get('username'));
            if ($request->request->get('email2')) {
                $user->setEmail2($request->request->get('email2'));
            }
            if ($request->request->get('email3')) {
                $user->setEmail3($request->request->get('email3'));
            }
            $user->setStraatnr($request->request->get('straatnr'));
            $user->setPostcode($request->request->get('postcode'));
            $user->setPlaats($request->request->get('plaats'));
            $user->setTel1($request->request->get('tel1'));
            if ($request->request->get('tel2')) {
                $user->setTel2($request->request->get('tel2'));
            }
            if ($request->request->get('tel3')) {
                $user->setTel3($request->request->get('tel3'));
            }

            $persoon->setUser($user);

            if ($newuser) {
                $password = $this->generatePassword();
                $encoder  = $this->container
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


            $message = new TemplatedEmail();
            $message->subject('Inloggegevens website Donar')
                ->from('webmaster@donargym.nl')
                ->to($user->getUsername())
                ->textTemplate('mails/new_user.txt.twig')
                ->context(
                    array(
                        'voornaam' => $persoon->getVoornaam(),
                        'email1'   => $user->getUsername(),
                        'email2'   => $user->getEmail2(),
                        'email3'   => $user->getEmail3(),
                        'password' => $password
                    )
                );
            $this->get('mailer')->send($message);

            if ($user->getEmail2()) {

                $message = new TemplatedEmail();
                $message->subject('Inloggegevens website Donar')
                    ->from('webmaster@donargym.nl')
                    ->to($user->getEmail2())
                    ->textTemplate('mails/new_user.txt.twig')
                    ->context(
                        array(
                            'voornaam' => $persoon->getVoornaam(),
                            'email1'   => $user->getUsername(),
                            'email2'   => $user->getEmail2(),
                            'email3'   => $user->getEmail3(),
                            'password' => $password
                        )
                    );
                $this->get('mailer')->send($message);
            }
            if ($user->getEmail3()) {

                $message = new TemplatedEmail();
                $message->subject('Inloggegevens website Donar')
                    ->from('webmaster@donargym.nl')
                    ->to($user->getEmail3())
                    ->textTemplate('mails/new_user.txt.twig')
                    ->context(
                        array(
                            'voornaam' => $persoon->getVoornaam(),
                            'email1'   => $user->getUsername(),
                            'email2'   => $user->getEmail2(),
                            'email3'   => $user->getEmail3(),
                            'password' => $password
                        )
                    );
                $this->get('mailer')->send($message);
            }
            return $this->redirectToRoute('getAdminSelectiePage');
        }
        return $this->render(
            'inloggen/adminAddTrainer.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'groepen'            => $groepenItems,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/selectie/edit/{id}/", name="editAdminTrainerPage", methods={"GET", "POST"})
     */
    public function editAdminTrainerPageAction(Request $request, $id)
    {
        $this->setBasicPageData();
        $em                         = $this->getDoctrine()->getManager();
        $query                      = $em->createQuery(
            'SELECT persoon
                FROM App:Persoon persoon
                WHERE persoon.id = :id'
        )
            ->setParameter('id', $id);
        $result                     = $query->setMaxResults(1)->getOneOrNullResult();
        $persoonEdit                = new \stdClass();
        $persoonEdit->voornaam      = $result->getVoornaam();
        $persoonEdit->achternaam    = $result->getAchternaam();
        $persoonEdit->geboortedatum = $result->getGeboortedatum();
        $user                       = $result->getUser();
        $persoonEdit->username      = $user->getUsername();
        $persoonEdit->email2        = $user->getEmail2();
        $persoonEdit->email3        = $user->getEmail3();
        $persoonEdit->userId        = $user->getId();
        $persoonEdit->straatnr      = $user->getStraatnr();
        $persoonEdit->postcode      = $user->getPostcode();
        $persoonEdit->plaats        = $user->getPlaats();
        $persoonEdit->tel1          = $user->getTel1();
        $persoonEdit->tel2          = $user->getTel2();
        $persoonEdit->tel3          = $user->getTel3();
        $functies                   = $result->getFunctie();
        $persoonEdit->functie       = array();
        for ($i = 0; $i < count($functies); $i++) {
            $persoonEdit->functie[$i]             = new \stdClass();
            $persoonEdit->functie[$i]->functie    = $functies[$i]->getFunctie();
            $groep                                = $functies[$i]->getGroep();
            $persoonEdit->functie[$i]->groepNaam  = $groep->getName();
            $persoonEdit->functie[$i]->groepId    = $groep->getId();
            $trainingen                           = $groep->getTrainingen();
            $persoonEdit->functie[$i]->trainingen = array();
            for ($j = 0; $j < count($trainingen); $j++) {
                $persoonTrainingen = $result->getTrainingen();
                for ($k = 0; $k < count($persoonTrainingen); $k++) {
                    if ($trainingen[$j]->getId() == $persoonTrainingen[$k]->getId()) {
                        $persoonEdit->functie[$i]->trainingen[$k]             = new \stdClass();
                        $persoonEdit->functie[$i]->trainingen[$k]->trainingId = $persoonTrainingen[$k]->getId();
                    }
                }
            }
        }

        $query = $em->createQuery(
            'SELECT groepen
                FROM App:Groepen groepen'
        );
        /** @var Groepen $groepen */
        $groepen      = $query->getResult();
        $groepenItems = array();
        for ($i = 0; $i < count($groepen); $i++) {
            $groepenItems[$i]             = new \stdClass();
            $groepenItems[$i]->id         = $groepen[$i]->getId();
            $groepenItems[$i]->naam       = $groepen[$i]->getName();
            $groepenItems[$i]->trainingen = array();
            $query                        = $em->createQuery(
                'SELECT trainingen
                FROM App:Trainingen trainingen
                WHERE trainingen.groep = :id'
            )
                ->setParameter('id', $groepen[$i]->getId());
            $trainingen                   = $query->getResult();
            for ($j = 0; $j < count($trainingen); $j++) {
                $groepenItems[$i]->trainingen[$j]          = new \stdClass();
                $groepenItems[$i]->trainingen[$j]->dag     = $trainingen[$j]->getDag();
                $groepenItems[$i]->trainingen[$j]->tijdVan = $trainingen[$j]->getTijdVan();
                $groepenItems[$i]->trainingen[$j]->tijdTot = $trainingen[$j]->getTijdTot();
                $groepenItems[$i]->trainingen[$j]->id      = $trainingen[$j]->getId();
            }
        }
        if ($request->getMethod() == 'POST') {
            $query = $em->createQuery(
                'SELECT persoon
                FROM App:Persoon persoon
                WHERE persoon.id = :id'
            )
                ->setParameter('id', $id);

            /** @var Persoon $persoon */
            $persoon = $query->setMaxResults(1)->getOneOrNullResult();
            $persoon->setVoornaam($request->request->get('voornaam'));
            $persoon->setAchternaam($request->request->get('achternaam'));
            $persoon->setGeboortedatum($request->request->get('geboortedatum'));

            /** @var Functie $functie */
            $functies = $persoon->getFunctie();
            foreach ($functies as $functie) {
                /** @var Groepen $groep */
                $groep = $functie->getGroep();
                if (!($request->request->get('groep_' . $groep->getId()) == 'Trainer' || $request->request->get(
                        'groep_' . $groep->getId()
                    ) == 'Assistent-Trainer')) {
                    $persoon->removeFunctie($functie);
                    $query = $em->createQuery(
                        'SELECT trainingen
                    FROM App:Trainingen trainingen
                    WHERE trainingen.groep = :id'
                    )
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
                if (!($request->request->get('trainingen_' . $training->getId()) == 'on')) {
                    $persoon->removeTrainingen($training);
                }
            }

            /** @var \App\Entity\User $user */
            $user = $persoon->getUser();
            $user->setUsername($request->request->get('username'));
            $user->setEmail2($request->request->get('email2'));
            $user->setEmail3($request->request->get('email3'));
            $user->setStraatnr($request->request->get('straatnr'));
            $user->setPostcode($request->request->get('postcode'));
            $user->setPlaats($request->request->get('plaats'));
            $user->setTel1($request->request->get('tel1'));
            $user->setTel2($request->request->get('tel2'));
            $user->setTel3($request->request->get('tel3'));

            foreach ($groepen as $groep) {
                $check = false;
                if ($request->request->get('groep_' . $groep->getId()) == 'Trainer' ||
                    $request->request->get('groep_' . $groep->getId()) == 'Assistent-Trainer') {
                    foreach ($functies as &$functie) {
                        /** @var Groepen $functieGroep */
                        $functieGroep = $functie->getGroep();
                        if ($functieGroep->getId() == $groep->getId()) {
                            $functie->setFunctie($request->request->get('groep_' . $groep->getId()));
                            $check = true;
                        }
                    }
                    if (!$check) {
                        $newFunctie = new Functie();
                        $newFunctie->setFunctie($request->request->get('groep_' . $groep->getId()));
                        $newFunctie->setGroep($groep);
                        $newFunctie->setPersoon($persoon);
                        $persoon->addFunctie($newFunctie);
                    }

                    $query = $em->createQuery(
                        'SELECT trainingen
                    FROM App:Trainingen trainingen
                    WHERE trainingen.groep = :id'
                    )
                        ->setParameter('id', $groep->getId());

                    /** @var Trainingen $dbTrainingen */
                    /** @var Trainingen $dbTraining */
                    $dbTrainingen = $query->getResult();
                    foreach ($dbTrainingen as $dbTraining) {
                        $trainingenCheck = false;
                        if ($request->request->get('trainingen_' . $dbTraining->getId()) == 'on') {
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
            $role     = 'ROLE_TURNSTER';
            $personen = $user->getPersoon();
            foreach ($personen as $persoonItem) {
                $functie = $persoonItem->getFunctie();
                if ($functie) {
                    foreach ($functie as $functieItem) {
                        if ($functieItem->getFunctie() == 'Trainer') {
                            $role = 'ROLE_TRAINER';
                        } elseif ($functieItem->getFunctie() == 'Assistent-Trainer' && $role == 'ROLE_TURNSTER') {
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
        return $this->render(
            'inloggen/adminEditTrainer.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'groepen'            => $groepenItems,
                'persoonEdit'        => $persoonEdit,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/selectie/remove/{id}/", name="removeAdminTrainerPage", methods={"GET", "POST"})
     */
    public function removeAdminTrainerPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT persoon
                FROM App:Persoon persoon
                WHERE persoon.id = :id'
            )
                ->setParameter('id', $id);
            $persoon = $query->setMaxResults(1)->getOneOrNullResult();
            if ($persoon) {
                return $this->render(
                    'inloggen/adminRemoveTrainer.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'voornaam'           => $persoon->getVoornaam(),
                        'achternaam'         => $persoon->getAchternaam(),
                        'id'                 => $persoon->getId(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'calendarItems'      => $this->calendarItems,
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em       = $this->getDoctrine()->getManager();
            $query    = $em->createQuery(
                'SELECT persoon
                FROM App:Persoon persoon
                WHERE persoon.id = :id'
            )
                ->setParameter('id', $id);
            $persoon  = $query->setMaxResults(1)->getOneOrNullResult();
            $user     = $persoon->getUser();
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
            return $this->redirectToRoute('getAdminSelectiePage');
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'calendarItems'      => $this->calendarItems,
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/admin/getAdminOwPage/index/", name="getAdminOwPage", methods={"GET", "POST"})
     */
    public function getAdminOwPage(Request $request)
    {
        $this->setBasicPageData();
        return $this->render(
            'inloggen/adminOwIndexPage.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/admin/getAdminOwPage/uploadWedstrijdindelingen/", name="uploadWedstrijdindelingen", methods={"GET", "POST"})
     */
    public function uploadWedstrijdindelingen(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            /** @var UserRepository $repo */
            $repo   = $this->getDoctrine()->getRepository(User::class);
            $result = $repo->findOneBy(array('username' => 'OWJurySysteem'));
            if (!$result) {
                $user = new User();
                $user->setUsername('OWJurySysteem');
                $user->setRole('ROLE_ORGANISATIE');
                $user->setIsActive(true);
                $user->setStraatnr(' ');
                $user->setPostcode(' ');
                $user->setPlaats(' ');
                $user->setTel1(' ');
                $password = $this->getParameter('JurySysteemWachtwoord');
                $encoder  = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $this->addToDB($user);
            }
            $result = $repo->findOneBy(array('username' => 'OWJuryLid'));
            if (!$result) {
                $user = new User();
                $user->setUsername('OWJuryLid');
                $user->setRole('ROLE_JURY');
                $user->setIsActive(true);
                $user->setStraatnr(' ');
                $user->setPostcode(' ');
                $user->setPlaats(' ');
                $user->setTel1(' ');
                $password = $this->getParameter('JurySysteemWachtwoord');
                $encoder  = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $this->addToDB($user);
            }
            if ($_FILES["userfile"]) {
                if (!empty($_FILES['userfile']['name'])) {
                    $allow[0] = "csv";
                    $extentie = strtolower(substr($_FILES['userfile']['name'], -3));
                    if ($extentie == $allow[0]) {
                        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                            if ($_FILES['userfile']['size'] < 5000000) {
                                $localfile = $_FILES['userfile']['tmp_name'];
                                ini_set("auto_detect_line_endings", "1");
                                if (($handle = fopen($localfile, "r")) !== FALSE) {
                                    $turnsters = $this->getDoctrine()->getRepository(Turnster::class)->findAll();
                                    foreach ($turnsters as $turnster) {
                                        $this->removeFromDB($turnster);
                                    }

                                    $repo = $this->getDoctrine()->getRepository(User::class);
                                    while (($lineData = fgetcsv($handle, 0, ";")) !== FALSE) {
                                        var_dump($lineData);
                                        /** @var User $user */
                                        $user = $repo->findOneBy(array('username' => trim($lineData[2])));
                                        if (!$user) {
                                            $user = new User();
                                            $user->setUsername(trim($lineData[2]));
                                            $user->setRole('ROLE_CONTACT');
                                            $user->setIsActive(true);
                                            $user->setStraatnr(' ');
                                            $user->setPostcode(' ');
                                            $user->setPlaats(' ');
                                            $user->setTel1(' ');
                                            $password = ' ';
                                            $encoder  = $this->container
                                                ->get('security.encoder_factory')
                                                ->getEncoder($user);
                                            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                                            $vereniging = new Vereniging();
                                            $vereniging->setNaam(trim($lineData[2]));
                                            $vereniging->setPlaats(' ');
                                            $this->addToDB($vereniging);
                                            $user->setVereniging($vereniging);
                                            $this->addToDB($user);
                                        }
                                        $turnster = new Turnster();
                                        $scores   = new Scores();
                                        $turnster->setWachtlijst(false);
                                        $turnster->setCreationDate(new \DateTime('now'));
                                        $turnster->setScores($scores);
                                        $turnster->setVoornaam(trim($lineData[1]));
                                        $turnster->setAchternaam(' ');
                                        $turnster->setNiveau(trim($lineData[4]));
                                        $turnster->setCategorie(trim($lineData[3]));
                                        $turnster->setIngevuld(true);
                                        $turnster->setUser($user);
                                        $user->addTurnster($turnster);
                                        /** @var Turnster $turnster */
                                        $scores->setWedstrijdnummer(trim($lineData[5]));
                                        $scores->setWedstrijddag(trim($lineData[6]));
                                        $scores->setWedstrijdronde(trim($lineData[7]));
                                        $scores->setBaan(trim($lineData[8]));
                                        $scores->setGroep(trim($lineData[9]));
                                        $scores->setBegintoestel(trim($lineData[9]));
                                        $this->addToDB($scores);
                                        $this->addToDB($turnster);
                                    }
                                    die;
                                    fclose($handle);
                                }

                                /** @var ScoresRepository $repo */
                                $repo    = $this->getDoctrine()->getRepository(Scores::class);
                                $results = $this->getDoctrine()->getRepository(ToegestaneNiveaus::class)
                                    ->findAll();
                                foreach ($results as $result) {
                                    $this->removeFromDB($result);
                                }
                                $results = $repo->getDistinctNiveaus();
                                foreach ($results as $result) {
                                    $new = new ToegestaneNiveaus();
                                    $new->setCategorie($result['categorie']);
                                    $new->setNiveau($result['niveau']);
                                    $new->setUitslagGepubliceerd(0);
                                    $this->addToDB($new);
                                }

                                return $this->redirectToRoute('getAdminOwPage');
                            } else {
                                $this->addFlash(
                                    'error',
                                    'Helaas, de upload is mislukt: het bestand is te groot.'
                                );
                            }
                        } else {
                            $this->addFlash(
                                'error',
                                'Helaas, de upload is mislukt.'
                            );
                        }
                    } else {
                        $this->addFlash(
                            'error',
                            'Helaas, de upload is mislukt: het bestand moet .csv zijn.'
                        );
                    }
                } else {
                    $this->addFlash(
                        'error',
                        'Selecteer een bestand.'
                    );
                }
            }
        }
        $this->setBasicPageData();
        return $this->render(
            'inloggen/uploadWedstrijdindelingen.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }
}
