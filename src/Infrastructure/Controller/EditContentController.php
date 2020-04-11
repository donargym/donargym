<?php

namespace App\Infrastructure\Controller;

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
use App\PublicInformation\Infrastructure\DoctrineDbal\DbalSimpleContentPageRepository;
use App\Shared\Domain\SystemClock;
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
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private SystemClock $clock;
    private RouterInterface $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twig,
        SystemClock $clock,
        RouterInterface $router
    )
    {
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
            return $this->redirectToRoute('newsPosts');
        } else {
            return $this->render(
                '@PublicInformation/default/addCalendar.html.twig',
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
                return $this->redirectToRoute('newsPosts');
            } else {
                return $this->render(
                    '@PublicInformation/default/addCalendar.html.twig',
                    array(
                        'form' => $form->createView(),
                    )
                );
            }
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('newsPosts');
        } else {
            return $this->render(
                '@PublicInformation/default/addNieuwsbericht.html.twig',
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
                return $this->redirectToRoute('newsPosts');
            } else {
                return $this->render(
                    '@PublicInformation/default/addNieuwsbericht.html.twig',
                    array(
                        'form' => $form->createView(),
                    )
                );
            }
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
                    '@PublicInformation/default/removeNieuwsbericht.html.twig',
                    array(
                        'content' => $nieuwsbericht->getAll(),
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('newsPosts');
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('holidays');
        } else {
            return $this->render(
                '@PublicInformation/default/addVakanties.html.twig',
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
                return $this->redirectToRoute('holidays');
            } else {
                return $this->render(
                    '@PublicInformation/default/addVakanties.html.twig',
                    array(
                        'form'          => $form->createView(),
                        'vakantieItems' => $vakantieItems,
                    )
                );
            }
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
                    '@PublicInformation/default/removeVakanties.html.twig',
                    array(
                        'content'       => $vakanties->getAll(),
                        'vakantieItems' => $vakantieItems,
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('holidays');
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('holidays');
        } else {
            return $this->render(
                '@PublicInformation/default/addClubblad.html.twig',
                array(
                    'form' => $form->createView(),
                )
            );
        }
    }

    /**
     * @Route("/subscription/formulieren/add/", name="addFormulierenPage", methods={"GET", "POST"})
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
            return $this->redirectToRoute('subscriptionPaperForms');
        } else {
            return $this->render(
                '@PublicInformation/subscription/addFormulieren.html.twig',
                array(
                    'form' => $form->createView(),
                )
            );
        }
    }

    /**
     * @Route("/subscription/formulieren/remove/{id}/", name="removeFormulierenPage", methods={"GET", "POST"})
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
                    '@PublicInformation/subscription/removeFormulieren.html.twig',
                    array(
                        'content' => $formulier->getAll(),
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('subscriptionPaperForms');
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('frequentlyAskedQuestions');
        } else {
            return $this->render(
                '@PublicInformation/contact/addVeelgesteldeVragen.html.twig',
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
                return $this->redirectToRoute('frequentlyAskedQuestions');
            } else {
                return $this->render(
                    '@PublicInformation/contact/addVeelgesteldeVragen.html.twig',
                    array(
                        'form' => $form->createView(),
                    )
                );
            }
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
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
                    '@PublicInformation/contact/removeVeelgesteldeVragen.html.twig',
                    array(
                        'content' => $vraag->getAll(),
                    )
                );
            } else {
                return $this->render(
                    '@Shared/error/page_not_found.html.twig',
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
            return $this->redirectToRoute('frequentlyAskedQuestions');
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
                array()
            );
        }
    }
}
