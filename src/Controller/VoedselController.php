<?php

namespace App\Controller;

use App\Entity\Persoon;
use App\Entity\Voedsel;
use App\Form\Type\VoedselType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_TURNSTER")
 */
class VoedselController extends SelectieController
{
    /**
     * @Route("/inloggen/selectie/{persoonId}/EtenDrinken/{groepId}/index/", name="EtenDrinkenIndex", methods={"GET"})
     */
    public function EtenDrinkenIndex($persoonId, $groepId)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject     = $this->getUser();
        $user           = $this->getBasisUserGegevens($userObject);
        $persoon        = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems   = $this->getOnePersoon($userObject, $persoonId);
        $em             = $this->getDoctrine()->getManager();
        $voedselObjects = $em->getRepository(Voedsel::class)->findAll();
        $voedsel        = array();
        for ($i = 0; $i < count($voedselObjects); $i++) {
            $voedsel[$i]                    = $voedselObjects[$i]->getAll();
            $persoonObject                  = $voedselObjects[$i]->getPersoon();
            $voedsel[$i]->persoonId         = $persoonObject->getId();
            $voedsel[$i]->persoonVoornaam   = $persoonObject->getVoornaam();
            $voedsel[$i]->persoonAchternaam = $persoonObject->getAchternaam();
        }
        return $this->render(
            'inloggen/viewVoedsel.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'persoon'            => $persoon,
                'user'               => $user,
                'persoonItems'       => $persoonItems,
                'wedstrijdLinkItems' => $this->groepItems,
                'groepId'            => $groepId,
                'voedselItems'       => $voedsel,
            )
        );
    }

    /**
     * @Route("/inloggen/selectie/{persoonId}/EtenDrinken/{groepId}/add/", name="EtenDrinkenAdd")
     * @Route("/inloggen/selectie/{persoonId}/EtenDrinken/{groepId}/edit/{voedselId}/", name="EtenDrinkenEdit", methods={"GET", "POST"})
     */
    public function EtenDrinkenAdd($persoonId, $groepId, $voedselId = false, Request $request)
    {
        $this->setBasicPageData('wedstrijdturnen');
        $userObject   = $this->getUser();
        $user         = $this->getBasisUserGegevens($userObject);
        $persoon      = $this->getBasisPersoonsGegevens($userObject);
        $persoonItems = $this->getOnePersoon($userObject, $persoonId);
        if ($voedselId) {
            $em            = $this->getDoctrine()->getManager();
            $voedsel       = $em->getRepository(Voedsel::class)->findOneBy(array('id' => $voedselId));
            $persoonObject = $voedsel->getPersoon();
            if ($persoonObject->getId() != $persoonId) {
                $voedsel = new Voedsel();
            }
        } else {
            $voedsel = new Voedsel();
        }
        $form = $this->createForm(VoedselType::class, $voedsel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $voedsel->setPersoon($em->getRepository(Persoon::class)->findOneBy(array('id' => $persoonId)));
            $em->persist($voedsel);
            $em->flush();

            return $this->redirectToRoute(
                'EtenDrinkenIndex',
                array(
                    'persoonId' => $persoonId,
                    'groepId'   => $groepId,
                )
            );
        }

        return $this->render(
            'inloggen/addVoedsel.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'persoon'            => $persoon,
                'user'               => $user,
                'persoonItems'       => $persoonItems,
                'wedstrijdLinkItems' => $this->groepItems,
                'groepId'            => $groepId,
                'form'               => $form->createView(),
            )
        );
    }

    /**
     * @Route("/inloggen/selectie/{persoonId}/EtenDrinken/{groepId}/remove/{voedselId}/", name="EtenDrinkenRemove", methods={"GET"})
     */
    public function EtenDrinkenRemove($persoonId, $groepId, $voedselId, Request $request)
    {
        $em            = $this->getDoctrine()->getManager();
        $voedsel       = $em->getRepository(Voedsel::class)->findOneBy(array('id' => $voedselId));
        $persoonObject = $voedsel->getPersoon();
        if ($persoonObject->getId() == $persoonId) {
            $em->remove($voedsel);
            $em->flush();
        }

        return $this->redirectToRoute(
            'EtenDrinkenIndex',
            array(
                'persoonId' => $persoonId,
                'groepId'   => $groepId,
            )
        );
    }
}
