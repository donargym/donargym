<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\SymfonyMailer;

use App\Shared\Domain\Security\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SymfonyResetPasswordMailer
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

    public function notify(User $user): void
    {
        $message = new TemplatedEmail();
        $message->subject($this->translator->trans('reset_password_mail.subject'))
            ->from($this->sender)
            ->to($user->getUsername())
            ->context(
                [
                    'setPasswordUrl' => $this->router->generate(
                        'setPassword',
                        ['passwordToken' => $user->passwordToken()->toString()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ]
            )
            ->textTemplate('@Shared/mails/reset_password.txt.twig');

        $this->mailer->send($message);
    }
}
