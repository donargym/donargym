<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\SymfonyMailer;

use App\Shared\Domain\Security\UserCredentials;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyPasswordConfirmationMailer
{
    private MailerInterface     $mailer;
    private TranslatorInterface $translator;
    private string              $sender;
    private RouterInterface     $router;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, RouterInterface $router)
    {
        $this->mailer     = $mailer;
        $this->translator = $translator;
        $this->sender     = 'noreply@donargym.nl';
        $this->router     = $router;
    }

    public function notify(UserCredentials $user): void
    {
        $message = new TemplatedEmail();
        $message->subject($this->translator->trans('password_confirmation_mail.subject'))
            ->from($this->sender)
            ->to($user->getUsername())
            ->textTemplate('@Shared/mails/password_was_set.txt.twig')
            ->context(
                [
                    'resetPasswordUrl' => $this->router->generate(
                        'getNewCredentials',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ]
            );
        $this->mailer->send($message);
    }
}
