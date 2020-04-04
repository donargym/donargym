<?php

namespace App\Infrastructure\SymfonyController;

use App\Domain\SystemClock;
use App\Entity\Calendar;
use App\Entity\Clubblad;
use App\Entity\Formulieren;
use App\Entity\Nieuwsbericht;
use App\Entity\Vakanties;
use App\Entity\VeelgesteldeVragen;
use App\Form\Type\CalendarType;
use App\Form\Type\NieuwsberichtType;
use App\Form\Type\VakantiesType;
use App\Form\Type\VeelgesteldeVragenType;
use App\Infrastructure\DoctrineDbal\DbalSimpleContentPageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class EditContentController extends BaseController
{
    private DbalSimpleContentPageRepository $simpleContentPageRepository;
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private SystemClock $clock;
    private RouterInterface $router;

    public function __construct(
        DbalSimpleContentPageRepository $simpleContentPageRepository,
        FormFactoryInterface $formFactory,
        Environment $twig,
        SystemClock $clock,
        RouterInterface $router
    )
    {
        $this->simpleContentPageRepository = $simpleContentPageRepository;
        $this->formFactory                 = $formFactory;
        $this->twig                        = $twig;
        $this->clock                       = $clock;
        $this->router                      = $router;
    }

    /**
     * @Route("/agenda/add/", name="addAgendaPage", methods={"GET", "POST"})
     */
    public function addAgendaPage(Request $request)
    {
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
                    'form' => $form->createView(),
                )
            );
        }
    }

    /**
     * @Route("/agenda/edit/{id}/", name="editAgendaPage", methods={"GET", "POST"})
     */
    public function editAgendaPage($id, Request $request)
    {
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
                        'form' => $form->createView(),
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/agenda/remove/{id}/", name="removeAgendaPage", methods={"GET", "POST"})
     */
    public function removeAgendaPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
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
                        'content' => $agenda->getAll(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
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
                array()
            );
        }
    }

    /**
     * @Route("/nieuws/index/add/", name="addNieuwsPage", methods={"GET", "POST"})
     */
    public function addNieuwsPage(Request $request)
    {
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
                    'form' => $form->createView(),
                )
            );
        }
    }

    /**
     * @Route("/nieuws/index/edit/{id}/", name="editNieuwsberichtPage", methods={"GET", "POST"})
     */
    public function editNieuwsberichtPage($id, Request $request)
    {
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
                        'form' => $form->createView(),
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/nieuws/index/remove/{id}/", name="removeNieuwsberichtPage", methods={"GET", "POST"})
     */
    public function removeNieuwsberichtPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
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
                        'content' => $nieuwsbericht->getAll(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
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
                array()
            );
        }
    }

    /**
     * @Route("/nieuws/vakanties/add/", name="addVakantiesPage", methods={"GET", "POST"})
     */
    public function addVakantiesPage(Request $request)
    {
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
                    'form'          => $form->createView(),
                    'vakantieItems' => $vakantieItems,
                )
            );
        }
    }

    /**
     * @Route("/nieuws/vakanties/edit/{id}/", name="editVakantiesPage", methods={"GET", "POST"})
     */
    public function editVakantiesPage($id, Request $request)
    {
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
                        'form'          => $form->createView(),
                        'vakantieItems' => $vakantieItems,
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/nieuws/vakanties/remove/{id}/", name="removeVakantiesPage", methods={"GET", "POST"})
     */
    public function removeVakantiesPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
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
                        'content'       => $vakanties->getAll(),
                        'vakantieItems' => $vakantieItems,
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
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
                array()
            );
        }
    }

    /**
     * @Route("/nieuws/clubblad/add/", name="addClubbladPage", methods={"GET", "POST"})
     */
    public function addClubbladPageAction(Request $request)
    {
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
                    'form' => $form->createView(),
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
                        'content' => $clubblad->getAll(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
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
                array()
            );
        }
    }

    /**
     * @Route("/lidmaatschap/formulieren/add/", name="addFormulierenPage", methods={"GET", "POST"})
     */
    public function addFormulierenPageAction(Request $request)
    {
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
                    'form' => $form->createView(),
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
                        'content' => $formulier->getAll(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
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
                array()
            );
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/add/", name="addVeelgesteldeVragenPage", methods={"GET", "POST"})
     */
    public function addVeelgesteldeVragenPage(Request $request)
    {
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
                    'form' => $form->createView(),
                )
            );
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/edit/{id}/", name="editVeelgesteldeVragenPage", methods={"GET", "POST"})
     */
    public function editVeelgesteldeVragenPage($id, Request $request)
    {
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
                        'form' => $form->createView(),
                    )
                );
            }
        } else {
            return $this->render(
                'error/pageNotFound.html.twig',
                array()
            );
        }
    }

    /**
     * @Route("/contact/veelgesteldevragen/remove/{id}/", name="removeVeelgesteldeVragenPage", methods={"GET", "POST"})
     */
    public function removeVeelgesteldeVragenPage($id, Request $request)
    {
        if ($request->getMethod() == 'GET') {
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
                        'content' => $vraag->getAll(),
                    )
                );
            } else {
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
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
                array()
            );
        }
    }
}
