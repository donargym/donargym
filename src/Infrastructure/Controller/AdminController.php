<?php

namespace App\Infrastructure\Controller;

use App\Entity\FileUpload;
use App\Entity\FotoUpload;
use App\Entity\Functie;
use App\Entity\Groepen;
use App\Entity\Persoon;
use App\Entity\Trainingen;
use App\Security\Domain\PasswordGenerator;
use App\Shared\Domain\EmailAddress;
use App\Shared\Domain\EmailTemplateType;
use App\Shared\Domain\ImageResizer;
use App\Shared\Infrastructure\SymfonyMailer\SymfonyMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends BaseController
{
    /**
     * @var SymfonyMailer
     */
    private SymfonyMailer $mailer;

    public function __construct(SymfonyMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/admin/", name="getAdminIndexPage", methods={"GET"})
     */
    public function getIndexPageAction()
    {
        return $this->render(
            'inloggen/adminIndex.html.twig',
            array()
        );
    }

    /**
     * @Route("/admin/foto/", name="getAdminFotoPage", methods={"GET"})
     */
    public function getAdminFotoPage()
    {
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
                'contentItems' => $contentItems,
            )
        );
    }

    /**
     * @Route("/admin/foto/add/", name="addAdminFotoPage", methods={"GET", "POST"})
     */
    public function addAdminFotoPageAction(Request $request)
    {
        $foto = new FotoUpload();
        $form = $this->createFormBuilder($foto)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                    'form' => $form->createView(),
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
                        'content' => $foto->getAll(),
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/pageNotFound.html.twig',
                    array()
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
                '@Shared/error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/admin/bestanden/", name="getAdminBestandenPage", methods={"GET"})
     */
    public function getAdminBestandenPage()
    {
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
                'contentItems' => $contentItems,
            )
        );
    }

    /**
     * @Route("/admin/bestanden/add/", name="addAdminBestandenPage", methods={"GET", "POST"})
     */
    public function addAdminBestandenPageAction(Request $request)
    {
        $file = new FileUpload();
        $form = $this->createFormBuilder($file)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($file);
            $em->flush();
            return $this->redirectToRoute('getAdminBestandenPage');
        } else {
            return $this->render(
                'inloggen/addAdminUploads.html.twig',
                array(
                    'form' => $form->createView(),
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
                        'content' => $file->getAll(),
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/pageNotFound.html.twig',
                    array()
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
                '@Shared/error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/admin/selectie/", name="getAdminSelectiePage", methods={"GET"})
     */
    public function getAdminSelectiePage()
    {
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
                'personen' => $persoonItems,
            )
        );
    }

    /**
     * @Route("/admin/selectie/add/", name="addAdminTrainerPage", methods={"GET", "POST"})
     */
    public function addAdminTrainerPageAction(Request $request, EncoderFactoryInterface $encoderFactory)
    {
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
                $password = PasswordGenerator::generatePassword();
                $encoder  = $encoderFactory
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em->persist($user);
            } else {
                $password = 'over een wachtwoord beschik je als het goed is al';
            }
            $user->addPersoon($persoon);
            $em->persist($persoon);
            $em->flush();

            $subject    = 'Inloggegevens website Donar';
            $template   = 'mails/new_user.txt.twig';
            $parameters = [
                'voornaam' => $persoon->getVoornaam(),
                'email1'   => $user->getUsername(),
                'email2'   => $user->getEmail2(),
                'email3'   => $user->getEmail3(),
                'password' => $password,
            ];

            $this->mailer->sendEmail(
                $subject,
                EmailAddress::fromString($user->getUsername()),
                $template,
                EmailTemplateType::TEXT(),
                $parameters
            );

            if ($user->getEmail2()) {
                $this->mailer->sendEmail(
                    $subject,
                    EmailAddress::fromString($user->getEmail2()),
                    $template,
                    EmailTemplateType::TEXT(),
                    $parameters
                );
            }
            if ($user->getEmail3()) {
                $this->mailer->sendEmail(
                    $subject,
                    EmailAddress::fromString($user->getEmail3()),
                    $template,
                    EmailTemplateType::TEXT(),
                    $parameters
                );
            }
            return $this->redirectToRoute('getAdminSelectiePage');
        }
        return $this->render(
            'inloggen/adminAddTrainer.html.twig',
            array(
                'groepen' => $groepenItems,
            )
        );
    }

    /**
     * @Route("/admin/selectie/edit/{id}/", name="editAdminTrainerPage", methods={"GET", "POST"})
     */
    public function editAdminTrainerPageAction(Request $request, $id)
    {
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
                'groepen'     => $groepenItems,
                'persoonEdit' => $persoonEdit,
            )
        );
    }

    /**
     * @Route("/admin/selectie/remove/{id}/", name="removeAdminTrainerPage", methods={"GET", "POST"})
     */
    public function removeAdminTrainerPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
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
                        'voornaam'   => $persoon->getVoornaam(),
                        'achternaam' => $persoon->getAchternaam(),
                        'id'         => $persoon->getId(),
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/pageNotFound.html.twig',
                    array()
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
                '@Shared/error/pageNotFound.html.twig',
                array()
            );
        }
    }
}
