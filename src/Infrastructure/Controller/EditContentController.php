<?php

namespace App\Infrastructure\Controller;

use App\Entity\Clubblad;
use App\Entity\Formulieren;
use App\Entity\Vakanties;
use App\Entity\VeelgesteldeVragen;
use App\Form\Type\VakantiesType;
use App\Form\Type\VeelgesteldeVragenType;
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
    private Environment          $twig;
    private SystemClock          $clock;
    private RouterInterface      $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twig,
        SystemClock $clock,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->twig        = $twig;
        $this->clock       = $clock;
        $this->router      = $router;
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
        $vakantieItems = [];
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
                [
                    'form'          => $form->createView(),
                    'vakantieItems' => $vakantieItems,
                ]
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
        $vakantieItems = [];
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
                    [
                        'form'          => $form->createView(),
                        'vakantieItems' => $vakantieItems,
                    ]
                );
            }
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
                []
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
                [
                    'widget' => 'single_text',
                ]
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
                [
                    'form' => $form->createView(),
                ]
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
                [
                    'form' => $form->createView(),
                ]
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
                [
                    'form' => $form->createView(),
                ]
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
                    [
                        'form' => $form->createView(),
                    ]
                );
            }
        } else {
            return $this->render(
                '@Shared/error/page_not_found.html.twig',
                []
            );
        }
    }
}
