<?php

declare(strict_types=1);

namespace App\Notification\Infrastructure\Service;

use App\Notification\Application\Service\EmailServiceInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerEmailService implements EmailServiceInterface
{
    private Address $from;

    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $env,
        private string $fromEmail,
        private string $fromName,
        private string $devToEmail
    ) {
        $this->from = new Address($this->fromEmail, $this->fromName);
    }

    public function sendForgotPasswordEmail(string $email, string $name, string $token): void
    {
        $this->send(
            new Address($email, $name),
            'Recuperar contraseña',
            'emails/forgot_password.html.twig',
            [
                'resetLink' => 'https://appquilar.com/reset-password?token=' . $token,
                'name' => $name
            ]
        );
    }

    private function send(
        Address $to,
        string $subject,
        string $template,
        array $context,
        Address $from = null
    ): void
    {
        $htmlContent = $this->twig->render($template, $context);

        $message = (new Email())
            ->from($from !== null ? $from : $this->from)
            ->to($to)
            ->subject($subject)
            ->html($htmlContent);

        if ($this->env === 'dev' && !str_contains($to->getAddress(), 'appquilar.com')) {
            $to = new Address($this->devToEmail, $to->getName());
            $message->to($to);
        }

        if ($this->env !== 'test') {
            $this->mailer->send($message);
        }
    }
}
