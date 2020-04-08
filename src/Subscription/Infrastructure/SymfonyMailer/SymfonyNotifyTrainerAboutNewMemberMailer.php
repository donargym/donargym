<?php

namespace App\Subscription\Infrastructure\SymfonyMailer;

use App\Subscription\Domain\Subscription;
use App\Subscription\Domain\TrainerOptions;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyNotifyTrainerAboutNewMemberMailer
{
    private MailerInterface     $mailer;
    private TranslatorInterface $translator;
    private string              $sender;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer     = $mailer;
        $this->translator = $translator;
        $this->sender     = 'noreply@donargym.nl';
    }

    public function notify(Subscription $subscription): void
    {
        $message = new TemplatedEmail();
        $message->subject($this->translator->trans('notify_trainer_about_new_member_mail.subject'))
            ->from($this->sender)
            ->to(TrainerOptions::findEmailAddressForTrainer($subscription->trainer())->toString())
            ->context($this->emailParameters($subscription))
            ->textTemplate('@Subscription/mails/subscription_notification_to_trainer.txt.twig');
        $this->mailer->send($message);
    }

    private function emailParameters(Subscription $subscription): array
    {
        return [
            'subscribedAt'            => $subscription->subscribedAt()->format('d-m-Y H:i'),
            'firstName'               => $subscription->firstName(),
            'lastName'                => $subscription->lastname(),
            'initials'                => $subscription->initials(),
            'dateOfBirth'             => $subscription->dateofbirth()->format('d-m-Y'),
            'gender'                  => $subscription->gender(),
            'address'                 => $subscription->address(),
            'zipCode'                 => $subscription->postcode(),
            'city'                    => $subscription->city(),
            'phone1'                  => $subscription->phone1(),
            'phone2'                  => $subscription->phone2(),
            'bankAccountNumber'       => $subscription->bankaccountnumber(),
            'bankAccountHolder'       => $subscription->bankaccountholder(),
            'emailAddress'            => $subscription->emailaddress(),
            'haveBeenSubscribed'      => $subscription->haveBeenSubscribed(),
            'subscribedFrom'          => $subscription->subscribedfrom()
                ? $subscription->subscribedfrom()->format('d-m-Y') : null,
            'subscribedUntil'         => $subscription->subscribeduntil()
                ? $subscription->subscribeduntil()->format('d-m-Y') : null,
            'otherClub'               => $subscription->otherClub(),
            'whatOtherClub'           => $subscription->whatotherclub(),
            'paidBondContribution'    => $subscription->paidBondContribution(),
            'category'                => $subscription->category(),
            'days'                    => implode(", ", $subscription->days()),
            'locations'               => implode(", ", $subscription->locations()),
            'startTime'               => $subscription->starttime()->format('H:i'),
            'trainer'                 => $subscription->trainer(),
            'how'                     => $subscription->how(),
            'voluntaryWork'           => $subscription->voluntaryWork(),
            'accept'                  => $subscription->accept(),
            'acceptPrivacy'           => $subscription->acceptPrivacyPolicy(),
            'acceptNamePublished'     => $subscription->acceptNamePublished(),
            'acceptPicturesPublished' => $subscription->acceptPicturesPublished(),
        ];
    }
}
