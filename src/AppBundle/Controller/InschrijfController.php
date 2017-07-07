<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inschrijving;
use AppBundle\Entity\InschrijvingRepository;
use AppBundle\Form\Type\SubscribeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

final class InschrijfController extends BaseController
{
    /**
     * @Route("/inschrijven/", name="subscribe")
     * @Method({"GET", "POST"})
     */
    public function subscribeAction(Request $request)
    {
        $this->setBasicPageData();
        $inschrijving = new Inschrijving();
        $form         = $this->createForm(new SubscribeType(), $inschrijving);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var InschrijvingRepository $repo */
            $repo = $this->getDoctrine()->getRepository("AppBundle:Inschrijving");

            $repo->saveInschrijving(
                $inschrijving,
                $this->container->getParameter('database_name'),
                $this->container->getParameter('database_user'),
                $this->container->getParameter('database_password'),
                $this->container->getParameter('database_host')
            );

            $subject = 'Inschrijving';
            $inschrijfDateTime = new \DateTime();
            $this->sendEmail(
                $subject,
                'ledensecretariaat@donargym.nl',
                'mails/inschrijving_naar_ledensecretariaat.txt.twig',
                array(
                    'inschrijfdatetime'  => $inschrijfDateTime->format('d-m-Y h:i'),
                    'voornaam'           => $inschrijving->getFirstName(),
                    'achternaam'         => $inschrijving->getLastname(),
                    'initialen'          => $inschrijving->getNameletters(),
                    'geboortedatum'      => $inschrijving->getDateofbirth()->format('d-m-Y'),
                    'geslacht'           => $inschrijving->getGender(),
                    'adres'              => $inschrijving->getAddress(),
                    'postcode'           => $inschrijving->getPostcode(),
                    'plaats'             => $inschrijving->getCity(),
                    'tel1'               => $inschrijving->getPhone1(),
                    'tel2'               => $inschrijving->getPhone2(),
                    'rekeningnummer'     => $inschrijving->getBankaccountnumber(),
                    'rekeninghouder'     => $inschrijving->getBankaccountholder(),
                    'emailadres'         => $inschrijving->getEmailaddress(),
                    'eerderingeschreven' => $inschrijving->isHavebeensubscribed(),
                    'ingeschrevenvan'    => $inschrijving->getSubscribedfrom() ? $inschrijving->getSubscribedfrom()->format('d-m-Y') : null,
                    'ingeschreventot'    => $inschrijving->getSubscribeduntil() ? $inschrijving->getSubscribeduntil()->format('d-m-Y') : null,
                    'andereclub'         => $inschrijving->isOtherclub(),
                    'welkeclub'          => $inschrijving->getWhatotherclub(),
                    'bondscontributie'   => $inschrijving->isBondscontributiebetaald(),
                    'categorie'          => $inschrijving->getCategory(),
                    'dagen'              => implode(", ", $inschrijving->getDays()),
                    'locaties'           => implode(", ", $inschrijving->getLocations()),
                    'starttijd'          => $inschrijving->getStarttime(),
                    'leiding'            => $inschrijving->getTrainer(),
                    'hoe'                => $inschrijving->getHow(),
                    'accept'             => $inschrijving->isAccept(),
                )
            );

            $this->sendEmail(
                $subject,
                $inschrijving->getEmailaddress(),
                'mails/inschrijving_naar_lid.txt.twig',
                array('voornaam' => $inschrijving->getFirstName())
            );

            $successMessage = 'Inschrijving succesvol verstuurd';
            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('getIndexPage');
        }

        return $this->render(
            'lidmaatschap/inschrijven.html.twig',
            array(
                'calendarItems'      => $this->calendarItems,
                'header'             => $this->header,
                'wedstrijdLinkItems' => $this->groepItems,
                'form'               => $form->createView(),
            )
        );
    }
}
