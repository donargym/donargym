<?php

namespace App\Infrastructure\Controller;

use App\Entity\Clubblad;
use App\Entity\Formulieren;
use App\Entity\VeelgesteldeVragen;
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
