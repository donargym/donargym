<?php

namespace App\Subscription\Infrastructure\SymfonyMailer;

use App\Subscription\Domain\Subscription;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyNewMemberConfirmationMailer
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
        $message->subject($this->translator->trans('new_member_confirmation_mail.subject'))
            ->from($this->sender)
            ->to($subscription->emailAddress())
            ->context(['firstName' => $subscription->firstName()])
            ->textTemplate('@Subscription/mails/new_member_confirmation.txt.twig');
        $this->mailer->send($message);
    }
}
