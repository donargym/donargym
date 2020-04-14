<?php

namespace App\Infrastructure\Controller;

use App\Entity\Clubblad;
use App\Entity\Formulieren;
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
}
