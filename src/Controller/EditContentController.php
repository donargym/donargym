<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Clubblad;
use App\Entity\Content;
use App\Entity\Formulieren;
use App\Entity\Nieuwsbericht;
use App\Entity\Vakanties;
use App\Entity\VeelgesteldeVragen;
use App\Form\Type\CalendarType;
use App\Form\Type\ContentType;
use App\Form\Type\NieuwsberichtType;
use App\Form\Type\VakantiesType;
use App\Form\Type\VeelgesteldeVragenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class EditContentController extends BaseController
{
    /**
     * @Route("/kamp/edit/", name="editKampPage", methods={"GET", "POST"})
     */
    public function editKampPageAction(Request $request)
    {
        $this->setBasicPageData();
        $em      = $this->getDoctrine()->getManager();
        $query   = $em->createQuery(
            'SELECT content
		FROM App:Content content
		WHERE content.pagina = :page
		ORDER BY content.gewijzigd DESC'
        )
            ->setParameter('page', 'kamp');
        $content = $query->setMaxResults(1)->getOneOrNullResult();
        if ($content) {
            $form = $this->createForm(ContentType::class, $content);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $editedContent = new Content();
                $editedContent->setGewijzigd(new \DateTime('NOW'));
                $editedContent->setPagina('kamp');
                $editedContent->setContent($content->getContent());
                $editedContent->setContent($content->getContent());
                $em->detach($content);
                $em->persist($editedContent);
                $em->flush();
                return $this->redirectToRoute('getKampPage');
            } else {
                return $this->render(
                    'kamp/editIndex.html.twig',
                    array(
                        'content'            => $content->getContent(),
                        'header'             => $this->header,
                        'form'               => $form->createView(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/donar/{page}/edit/", defaults={"page" = "geschiedenis"}, name="editDonarPage", methods={"GET", "POST"})
     */
    public function editDonarPageAction($page, Request $request)
    {
        $this->setBasicPageData();
        if (in_array(
            $page,
            array(
                'geschiedenis',
                'visie',
                'bestuur',
                'leiding',
                'evenementen',
                'locaties',
                'kleding',
                'vacatures',
                'sponsors'
            )
        )) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
            FROM App:Content content
            WHERE content.pagina = :page
            ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getDonarPage', array('page' => $page));
                } else {
                    return $this->render(
                        'donar/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/lessen/{page}/edit/", defaults={"page" = "lesrooster"}, name="editLessenPage", methods={"GET", "POST"})
     */
    public function editLessenPageAction($page, Request $request)
    {
        $this->setBasicPageData('recreatie');
        if (in_array(
            $page,
            array(
                'lesrooster',
                'peuterenkleutergym',
                'gymnastiekenrecreatiefturnen',
                '50plusgymenconditie',
                'aerobicsenbodyshape',
                'badmintonenvolleybal'
            )
        )) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getLessenPage', array('page' => $page));
                } else {
                    return $this->render(
                        'lessen/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/wedstrijdturnen/{page}/edit/",
     * defaults={"page" = "wedstrijdturnen"}, name="editWedstrijdturnenPage", methods={"GET", "POST"})
     */
    public function editWedstrijdturnenPageAction($page, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        if (in_array($page, array('wedstrijdturnen'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getWedstrijdturnenPage', array('page' => $page));
                } else {
                    return $this->render(
                        'wedstrijdturnen/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } elseif (in_array(
            $page,
            array(
                'voorselectiedenhaag',
                'voorselectieleidschendam',
                'aselectiedenhaag',
                'aselectieleidschendam',
                'bselectiedenhaag'
            )
        )) {

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/lidmaatschap/{page}/edit/", defaults={"page" = "lidmaatschap"}, name="editLidmaatschapPage", methods={"GET", "POST"})
     */
    public function editLidmaatschapPageAction($page, Request $request)
    {
        $this->setBasicPageData();
        if (in_array($page, array('lidmaatschap', 'contributie', 'formulieren', 'ooievaarspas'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getLidmaatschapPage', array('page' => $page));
                } else {
                    return $this->render(
                        'lidmaatschap/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/fotofilm/{page}/edit/", defaults={"page" = "fotoenfilm"}, name="editFotofilmPage", methods={"GET", "POST"})
     */
    public function editFotofilmPageAction($page, Request $request)
    {
        $this->setBasicPageData();
        if (in_array($page, array('fotoenfilm', 'foto', 'film'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getFotofilmPage', array('page' => $page));
                } else {
                    return $this->render(
                        'fotofilm/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/vrijwilligers/{page}/edit/", defaults={"page" = "vrijwilligers"}, name="editVrijwilligersPage", methods={"GET", "POST"})
     */
    public function editVrijwilligersPageAction($page, Request $request)
    {
        $this->setBasicPageData();
        if (in_array($page, array('vrijwilligers', 'taken', 'vrijwilligersdag'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getVrijwilligersPage', array('page' => $page));
                } else {
                    return $this->render(
                        'vrijwilligers/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/contact/{page}/edit/", defaults={"page" = "contact"}, name="editContactPage", methods={"GET", "POST"})
     */
    public function editContactPageAction($page, Request $request)
    {
        $this->setBasicPageData();
        if (in_array($page, array('contact', 'veelgesteldevragen'))) {
            $em      = $this->getDoctrine()->getManager();
            $query   = $em->createQuery(
                'SELECT content
                FROM App:Content content
                WHERE content.pagina = :page
                ORDER BY content.gewijzigd DESC'
            )
                ->setParameter('page', $page);
            $content = $query->setMaxResults(1)->getOneOrNullResult();
            if ($content) {
                $form = $this->createForm(ContentType::class, $content);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $editedContent = new Content();
                    $editedContent->setGewijzigd(new \DateTime('NOW'));
                    $editedContent->setPagina($page);
                    $editedContent->setContent($content->getContent());
                    $editedContent->setContent($content->getContent());
                    $em->detach($content);
                    $em->persist($editedContent);
                    $em->flush();
                    return $this->redirectToRoute('getContactPage', array('page' => $page));
                } else {
                    return $this->render(
                        'contact/editIndex.html.twig',
                        array(
                            'content'            => $content->getContent(),
                            'header'             => $this->header,
                            'form'               => $form->createView(),
                            'wedstrijdLinkItems' => $this->groepItems,
                        )
                    );
                }
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }

        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/agenda/add/", name="addAgendaPage", methods={"GET", "POST"})
     */
    public function addAgendaPage(Request $request)
    {
        $this->setBasicPageData();
        $agenda = new Calendar();
        $form   = $this->createForm(CalendarType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($agenda);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
        } else {
            return $this->render(
                'default/addCalendar.html.twig',
                array(
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/agenda/edit/{id}/", name="editAgendaPage", methods={"GET", "POST"})
     */
    public function editAgendaPage($id, Request $request)
    {
        $this->setBasicPageData();
        $em     = $this->getDoctrine()->getManager();
        $query  = $em->createQuery(
            'SELECT calendar
                FROM App:Calendar calendar
                WHERE calendar.id = :id'
        )
            ->setParameter('id', $id);
        $agenda = $query->setMaxResults(1)->getOneOrNullResult();
        if ($agenda) {
            $form = $this->createForm(CalendarType::class, $agenda);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($agenda);
                $em->flush();
                return $this->redirectToRoute('getNieuwsPage');
            } else {
                return $this->render(
                    'default/addCalendar.html.twig',
                    array(
                        'header'             => $this->header,
                        'form'               => $form->createView(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/agenda/remove/{id}/", name="removeAgendaPage", methods={"GET", "POST"})
     */
    public function removeAgendaPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em     = $this->getDoctrine()->getManager();
            $query  = $em->createQuery(
                'SELECT calendar
                FROM App:Calendar calendar
                WHERE calendar.id = :id'
            )
                ->setParameter('id', $id);
            $agenda = $query->setMaxResults(1)->getOneOrNullResult();
            if ($agenda) {
                return $this->render(
                    'default/removeCalendar.html.twig',
                    array(
                        'header'             => $this->header,
                        'content'            => $agenda->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em     = $this->getDoctrine()->getManager();
            $query  = $em->createQuery(
                'SELECT calendar
                FROM App:Calendar calendar
                WHERE calendar.id = :id'
            )
                ->setParameter('id', $id);
            $agenda = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($agenda);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/index/add/", name="addNieuwsPage", methods={"GET", "POST"})
     */
    public function addNieuwsPage(Request $request)
    {
        $this->setBasicPageData();
        $nieuwsbericht = new Nieuwsbericht();
        $form          = $this->createForm(NieuwsberichtType::class, $nieuwsbericht);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nieuwsbericht->setDatumtijd(date('d-m-Y: H:i', time()));
            $nieuwsbericht->setJaar(date("Y", time()));
            $nieuwsbericht->setBericht(str_replace("\n", "<br />", $nieuwsbericht->getBericht()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($nieuwsbericht);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
        } else {
            return $this->render(
                'default/addNieuwsbericht.html.twig',
                array(
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/index/edit/{id}/", name="editNieuwsberichtPage", methods={"GET", "POST"})
     */
    public function editNieuwsberichtPage($id, Request $request)
    {
        $this->setBasicPageData();
        $em            = $this->getDoctrine()->getManager();
        $query         = $em->createQuery(
            'SELECT nieuwsbericht
                FROM App:Nieuwsbericht nieuwsbericht
                WHERE nieuwsbericht.id = :id'
        )
            ->setParameter('id', $id);
        $nieuwsbericht = $query->setMaxResults(1)->getOneOrNullResult();
        $nieuwsbericht->setBericht(str_replace("<br />", "\n", $nieuwsbericht->getBericht()));
        if ($nieuwsbericht) {
            $form = $this->createForm(NieuwsberichtType::class, $nieuwsbericht);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $nieuwsbericht->setBericht(str_replace("\n", "<br />", $nieuwsbericht->getBericht()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($nieuwsbericht);
                $em->flush();
                return $this->redirectToRoute('getNieuwsPage');
            } else {
                return $this->render(
                    'default/addNieuwsbericht.html.twig',
                    array(
                        'header'             => $this->header,
                        'form'               => $form->createView(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/index/remove/{id}/", name="removeNieuwsberichtPage", methods={"GET", "POST"})
     */
    public function removeNieuwsberichtPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em            = $this->getDoctrine()->getManager();
            $query         = $em->createQuery(
                'SELECT nieuwsbericht
                FROM App:Nieuwsbericht nieuwsbericht
                WHERE nieuwsbericht.id = :id'
            )
                ->setParameter('id', $id);
            $nieuwsbericht = $query->setMaxResults(1)->getOneOrNullResult();
            if ($nieuwsbericht) {
                return $this->render(
                    'default/removeNieuwsbericht.html.twig',
                    array(
                        'header'             => $this->header,
                        'content'            => $nieuwsbericht->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em            = $this->getDoctrine()->getManager();
            $query         = $em->createQuery(
                'SELECT nieuwsbericht
                FROM App:Nieuwsbericht nieuwsbericht
                WHERE nieuwsbericht.id = :id'
            )
                ->setParameter('id', $id);
            $nieuwsbericht = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($nieuwsbericht);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage');
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/vakanties/add/", name="addVakantiesPage", methods={"GET", "POST"})
     */
    public function addVakantiesPage(Request $request)
    {
        $this->setBasicPageData();
        $em            = $this->getDoctrine()->getManager();
        $query         = $em->createQuery(
            'SELECT vakanties
            FROM App:Vakanties vakanties
            WHERE vakanties.tot >= :datum
            ORDER BY vakanties.van'
        )
            ->setParameter('datum', date('Y-m-d', time()));
        $content       = $query->getResult();
        $vakantieItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $vakantieItems[$i] = $content[$i]->getAll();
        }
        $vakanties = new Vakanties();
        $form      = $this->createForm(VakantiesType::class, $vakanties);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vakanties);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'vakanties'));
        } else {
            return $this->render(
                'default/addVakanties.html.twig',
                array(
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'vakantieItems'      => $vakantieItems,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/vakanties/edit/{id}/", name="editVakantiesPage", methods={"GET", "POST"})
     */
    public function editVakantiesPage($id, Request $request)
    {
        $this->setBasicPageData();
        $em            = $this->getDoctrine()->getManager();
        $query         = $em->createQuery(
            'SELECT vakanties
            FROM App:Vakanties vakanties
            WHERE vakanties.tot >= :datum
            ORDER BY vakanties.van'
        )
            ->setParameter('datum', date('Y-m-d', time()));
        $content       = $query->getResult();
        $vakantieItems = array();
        for ($i = 0; $i < count($content); $i++) {
            $vakantieItems[$i] = $content[$i]->getAll();
        }
        $query     = $em->createQuery(
            'SELECT vakanties
                FROM App:Vakanties vakanties
                WHERE vakanties.id = :id'
        )
            ->setParameter('id', $id);
        $vakanties = $query->setMaxResults(1)->getOneOrNullResult();
        if ($vakanties) {
            $form = $this->createForm(VakantiesType::class, $vakanties);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($vakanties);
                $em->flush();
                return $this->redirectToRoute('getNieuwsPage', array('page' => 'vakanties'));
            } else {
                return $this->render(
                    'default/addVakanties.html.twig',
                    array(
                        'header'             => $this->header,
                        'form'               => $form->createView(),
                        'vakantieItems'      => $vakantieItems,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/vakanties/remove/{id}/", name="removeVakantiesPage", methods={"GET", "POST"})
     */
    public function removeVakantiesPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em            = $this->getDoctrine()->getManager();
            $query         = $em->createQuery(
                'SELECT vakanties
            FROM App:Vakanties vakanties
            WHERE vakanties.tot >= :datum
            ORDER BY vakanties.van'
            )
                ->setParameter('datum', date('Y-m-d', time()));
            $content       = $query->getResult();
            $vakantieItems = array();
            for ($i = 0; $i < count($content); $i++) {
                $vakantieItems[$i] = $content[$i]->getAll();
            }
            $query     = $em->createQuery(
                'SELECT vakanties
                FROM App:Vakanties vakanties
                WHERE vakanties.id = :id'
            )
                ->setParameter('id', $id);
            $vakanties = $query->setMaxResults(1)->getOneOrNullResult();
            if ($vakanties) {
                return $this->render(
                    'default/removeVakanties.html.twig',
                    array(
                        'header'             => $this->header,
                        'content'            => $vakanties->getAll(),
                        'vakantieItems'      => $vakantieItems,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em        = $this->getDoctrine()->getManager();
            $query     = $em->createQuery(
                'SELECT vakanties
                FROM App:Vakanties vakanties
                WHERE vakanties.id = :id'
            )
                ->setParameter('id', $id);
            $vakanties = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($vakanties);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'vakanties'));
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/clubblad/add/", name="addClubbladPage", methods={"GET", "POST"})
     */
    public function addClubbladPageAction(Request $request)
    {
        $this->setBasicPageData();
        $clubblad = new Clubblad();
        $form     = $this->createFormBuilder($clubblad)
            ->add(
                'datum',
                DateType::class,
                array(
                    'widget' => 'single_text',
                )
            )
            ->add('file')
            ->add('uploadBestand', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clubblad);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'clubblad'));
        } else {
            return $this->render(
                'default/addClubblad.html.twig',
                array(
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/clubblad/remove/{id}/", name="removeClubbladPage", methods={"GET", "POST"})
     */
    public function removeClubbladPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em       = $this->getDoctrine()->getManager();
            $query    = $em->createQuery(
                'SELECT clubblad
                FROM App:Clubblad clubblad
                WHERE clubblad.id = :id'
            )
                ->setParameter('id', $id);
            $clubblad = $query->setMaxResults(1)->getOneOrNullResult();
            if ($clubblad) {
                return $this->render(
                    'default/removeClubblad.html.twig',
                    array(
                        'header'             => $this->header,
                        'content'            => $clubblad->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em       = $this->getDoctrine()->getManager();
            $query    = $em->createQuery(
                'SELECT clubblad
                FROM App:Clubblad clubblad
                WHERE clubblad.id = :id'
            )
                ->setParameter('id', $id);
            $clubblad = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($clubblad);
            $em->flush();
            return $this->redirectToRoute('getNieuwsPage', array('page' => 'clubblad'));
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/lidmaatschap/formulieren/add/", name="addFormulierenPage", methods={"GET", "POST"})
     */
    public function addFormulierenPageAction(Request $request)
    {
        $this->setBasicPageData();
        $formulier = new Formulieren();
        $form      = $this->createFormBuilder($formulier)
            ->add('naam')
            ->add('file')
            ->add('uploadBestand', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($formulier);
            $em->flush();
            return $this->redirectToRoute('getLidmaatschapPage', array('page' => 'formulieren'));
        } else {
            return $this->render(
                'lidmaatschap/addFormulieren.html.twig',
                array(
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/lidmaatschap/formulieren/remove/{id}/", name="removeFormulierenPage", methods={"GET", "POST"})
     */
    public function removeFormulierenPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em        = $this->getDoctrine()->getManager();
            $query     = $em->createQuery(
                'SELECT formulieren
                FROM App:Formulieren formulieren
                WHERE formulieren.id = :id'
            )
                ->setParameter('id', $id);
            $formulier = $query->setMaxResults(1)->getOneOrNullResult();
            if ($formulier) {
                return $this->render(
                    'lidmaatschap/removeFormulieren.html.twig',
                    array(
                        'header'             => $this->header,
                        'content'            => $formulier->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em        = $this->getDoctrine()->getManager();
            $query     = $em->createQuery(
                'SELECT formulieren
                FROM App:Formulieren formulieren
                WHERE formulieren.id = :id'
            )
                ->setParameter('id', $id);
            $formulier = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($formulier);
            $em->flush();
            return $this->redirectToRoute('getLidmaatschapPage', array('page' => 'formulieren'));
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/add/", name="addVeelgesteldeVragenPage", methods={"GET", "POST"})
     */
    public function addVeelgesteldeVragenPage(Request $request)
    {
        $this->setBasicPageData();
        $vraag = new VeelgesteldeVragen();
        $form  = $this->createForm(VeelgesteldeVragenType::class, $vraag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vraag);
            $em->flush();
            return $this->redirectToRoute('getContactPage', array('page' => 'veelgesteldevragen'));
        } else {
            return $this->render(
                'contact/addVeelgesteldeVragen.html.twig',
                array(
                    'header'             => $this->header,
                    'form'               => $form->createView(),
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/edit/{id}/", name="editVeelgesteldeVragenPage", methods={"GET", "POST"})
     */
    public function editVeelgesteldeVragenPage($id, Request $request)
    {
        $this->setBasicPageData();
        $em    = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT veelgesteldevragen
                FROM App:VeelgesteldeVragen veelgesteldevragen
                WHERE veelgesteldevragen.id = :id'
        )
            ->setParameter('id', $id);
        $vraag = $query->setMaxResults(1)->getOneOrNullResult();
        if ($vraag) {
            $form = $this->createForm(VeelgesteldeVragenType::class, $vraag);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($vraag);
                $em->flush();
                return $this->redirectToRoute('getContactPage', array('page' => 'veelgesteldevragen'));
            } else {
                return $this->render(
                    'contact/addVeelgesteldeVragen.html.twig',
                    array(
                        'header'             => $this->header,
                        'form'               => $form->createView(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/remove/{id}/", name="removeVeelgesteldeVragenPage", methods={"GET", "POST"})
     */
    public function removeVeelgesteldeVragenPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
            $this->setBasicPageData();
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT veelgesteldevragen
                FROM App:VeelgesteldeVragen veelgesteldevragen
                WHERE veelgesteldevragen.id = :id'
            )
                ->setParameter('id', $id);
            $vraag = $query->setMaxResults(1)->getOneOrNullResult();
            if ($vraag) {
                return $this->render(
                    'contact/removeVeelgesteldeVragen.html.twig',
                    array(
                        'header'             => $this->header,
                        'content'            => $vraag->getAll(),
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array(
                        'header'             => $this->header,
                        'wedstrijdLinkItems' => $this->groepItems,
                    )
                );
            }
        } elseif ($request->getMethod() == 'POST') {
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT veelgesteldevragen
                FROM App:VeelgesteldeVragen veelgesteldevragen
                WHERE veelgesteldevragen.id = :id'
            )
                ->setParameter('id', $id);
            $vraag = $query->setMaxResults(1)->getOneOrNullResult();
            $em->remove($vraag);
            $em->flush();
            return $this->redirectToRoute('getContactPage', array('page' => 'veelgesteldevragen'));
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array(
                    'header'             => $this->header,
                    'wedstrijdLinkItems' => $this->groepItems,
                )
            );
        }
    }
}
