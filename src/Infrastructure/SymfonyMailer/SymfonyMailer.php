<?php

declare(strict_types=1);

namespace App\Infrastructure\SymfonyMailer;

use App\Domain\EmailAddress;
use App\Domain\EmailTemplateType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class SymfonyMailer
{
    private MailerInterface $mailer;

    private string $sender;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->sender = 'noreply@donargym.nl';
    }

    public function sendEmail(
        string $subject,
        EmailAddress $recipientAddress,
        string $templateLocation,
        EmailTemplateType $templateType,
        array $parameters = []
    ): void
    {
        if (!$recipientAddress->isValid()) {
            return;
        }

        $message = new TemplatedEmail();

        switch ($templateType->toString()) {
            case EmailTemplateType::HTML:
                $message->htmlTemplate($templateLocation);
                break;
            case EmailTemplateType::TEXT:
                $message->textTemplate($templateLocation);
                break;
            default:
                return;
        }

        $message->subject($subject)
            ->from($this->sender)
            ->to($recipientAddress->toString())
            ->context(['parameters' => $parameters]);

        $this->mailer->send($message);
    }
}
