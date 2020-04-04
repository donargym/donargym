<?php

namespace App\Infrastructure\SymfonyController;

use App\Domain\EmailAddress;
use App\Domain\EmailTemplateType;
use App\Entity\Inschrijving;
use App\Form\Type\SubscribeType;
use App\Infrastructure\SymfonyMailer\SymfonyMailer;
use App\Repository\InschrijvingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class InschrijfController extends BaseController
{
    private SymfonyMailer $mailer;

    public function __construct(SymfonyMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/inschrijven/", name="subscribe", methods={"GET", "POST"})
     */
    public function subscribeAction(Request $request)
    {
        $inschrijving = new Inschrijving();
        $form         = $this->createForm(SubscribeType::class, $inschrijving);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var InschrijvingRepository $repo */
            $repo = $this->getDoctrine()->getRepository(Inschrijving::class);

            $repo->saveInschrijving($inschrijving);


            $trainers    = Inschrijving::trainerOptions();
            $trainerName = array_search($inschrijving->getTrainer(), $trainers);

            $subject           = 'Inschrijving';
            $inschrijfDateTime = new \DateTime();
            $this->mailer->sendEmail(
                $subject,
                EmailAddress::fromString('ledensecretariaat@donargym.nl'),
                'mails/inschrijving_naar_ledensecretariaat.txt.twig',
                EmailTemplateType::TEXT(),
                [
                    'inschrijfdatetime'       => $inschrijfDateTime->format('d-m-Y H:i'),
                    'voornaam'                => $inschrijving->getFirstName(),
                    'achternaam'              => $inschrijving->getLastname(),
                    'initialen'               => $inschrijving->getNameletters(),
                    'geboortedatum'           => $inschrijving->getDateofbirth()->format('d-m-Y'),
                    'geslacht'                => $inschrijving->getGender(),
                    'adres'                   => $inschrijving->getAddress(),
                    'postcode'                => $inschrijving->getPostcode(),
                    'plaats'                  => $inschrijving->getCity(),
                    'tel1'                    => $inschrijving->getPhone1(),
                    'tel2'                    => $inschrijving->getPhone2(),
                    'rekeningnummer'          => $inschrijving->getBankaccountnumber(),
                    'rekeninghouder'          => $inschrijving->getBankaccountholder(),
                    'emailadres'              => $inschrijving->getEmailaddress(),
                    'eerderingeschreven'      => $inschrijving->isHavebeensubscribed(),
                    'ingeschrevenvan'         => $inschrijving->getSubscribedfrom() ?
                        $inschrijving->getSubscribedfrom()->format('d-m-Y') : null,
                    'ingeschreventot'         => $inschrijving->getSubscribeduntil() ?
                        $inschrijving->getSubscribeduntil()->format('d-m-Y') : null,
                    'andereclub'              => $inschrijving->isOtherclub(),
                    'welkeclub'               => $inschrijving->getWhatotherclub(),
                    'bondscontributie'        => $inschrijving->isBondscontributiebetaald(),
                    'categorie'               => $inschrijving->getCategory(),
                    'dagen'                   => implode(", ", $inschrijving->getDays()),
                    'locaties'                => implode(", ", $inschrijving->getLocations()),
                    'starttijd'               => $inschrijving->getStarttime(),
                    'leiding'                 => $trainerName,
                    'hoe'                     => $inschrijving->getHow(),
                    'vrijwilligerstaken'      => $inschrijving->getVrijwilligerstaken(),
                    'accept'                  => $inschrijving->isAccept(),
                    'acceptPrivacy'           => $inschrijving->isAcceptPrivacyPolicy(),
                    'acceptNamePublished'     => $inschrijving->isAcceptNamePublished(),
                    'acceptPicturesPublished' => $inschrijving->isAcceptPicturesPublished(),
                ]
            );

            $this->mailer->sendEmail(
                $subject,
                EmailAddress::fromString($inschrijving->getTrainer()),
                'mails/inschrijving_naar_ledensecretariaat.txt.twig',
                EmailTemplateType::TEXT(),
                [
                    'inschrijfdatetime'       => $inschrijfDateTime->format('d-m-Y H:i'),
                    'voornaam'                => $inschrijving->getFirstName(),
                    'achternaam'              => $inschrijving->getLastname(),
                    'initialen'               => $inschrijving->getNameletters(),
                    'geboortedatum'           => $inschrijving->getDateofbirth()->format('d-m-Y'),
                    'geslacht'                => $inschrijving->getGender(),
                    'adres'                   => $inschrijving->getAddress(),
                    'postcode'                => $inschrijving->getPostcode(),
                    'plaats'                  => $inschrijving->getCity(),
                    'tel1'                    => $inschrijving->getPhone1(),
                    'tel2'                    => $inschrijving->getPhone2(),
                    'rekeningnummer'          => $inschrijving->getBankaccountnumber(),
                    'rekeninghouder'          => $inschrijving->getBankaccountholder(),
                    'emailadres'              => $inschrijving->getEmailaddress(),
                    'eerderingeschreven'      => $inschrijving->isHavebeensubscribed(),
                    'ingeschrevenvan'         => $inschrijving->getSubscribedfrom() ?
                        $inschrijving->getSubscribedfrom()->format('d-m-Y') : null,
                    'ingeschreventot'         => $inschrijving->getSubscribeduntil() ?
                        $inschrijving->getSubscribeduntil()->format('d-m-Y') : null,
                    'andereclub'              => $inschrijving->isOtherclub(),
                    'welkeclub'               => $inschrijving->getWhatotherclub(),
                    'bondscontributie'        => $inschrijving->isBondscontributiebetaald(),
                    'categorie'               => $inschrijving->getCategory(),
                    'dagen'                   => implode(", ", $inschrijving->getDays()),
                    'locaties'                => implode(", ", $inschrijving->getLocations()),
                    'starttijd'               => $inschrijving->getStarttime(),
                    'leiding'                 => $trainerName,
                    'hoe'                     => $inschrijving->getHow(),
                    'vrijwilligerstaken'      => $inschrijving->getVrijwilligerstaken(),
                    'accept'                  => $inschrijving->isAccept(),
                    'acceptPrivacy'           => $inschrijving->isAcceptPrivacyPolicy(),
                    'acceptNamePublished'     => $inschrijving->isAcceptNamePublished(),
                    'acceptPicturesPublished' => $inschrijving->isAcceptPicturesPublished(),
                ]
            );

            $this->mailer->sendEmail(
                $subject,
                EmailAddress::fromString($inschrijving->getEmailaddress()),
                'mails/inschrijving_naar_lid.txt.twig',
                EmailTemplateType::TEXT(),
                array('voornaam' => $inschrijving->getFirstName())
            );

            $successMessage = 'Inschrijving succesvol verstuurd';
            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('getIndexPage');
        }

        return $this->render(
            'subscription/inschrijven.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
