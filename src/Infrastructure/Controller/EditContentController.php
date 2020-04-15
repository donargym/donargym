<?php

namespace App\Infrastructure\Controller;

use App\Entity\Formulieren;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class EditContentController extends AbstractController
{
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
