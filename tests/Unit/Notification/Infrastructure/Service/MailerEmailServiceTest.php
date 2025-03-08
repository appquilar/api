<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Infrastructure\Service;

use App\Notification\Infrastructure\Service\MailerEmailService;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerEmailServiceTest extends UnitTestCase
{
    private MailerInterface $mailerMock;
    private Environment $twigMock;
    private MailerEmailService $mailerService;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->twigMock = $this->createMock(Environment::class);

        $this->mailerService = new MailerEmailService(
            $this->mailerMock,
            $this->twigMock,
            'prod',
            'noreply@appquilar.com',
            'Appquilar Support',
            'devtest@appquilar.com'
        );
    }

    public function testSendForgotPasswordEmail(): void
    {
        $email = 'user@example.com';
        $name = 'Test User';
        $token = 'test-token-123';
        $resetLink = 'https://appquilar.com/reset-password?token=' . $token;
        $htmlContent = '<html>Mocked Email Content</html>';
        $emailToBeSent = (new Email())
            ->from(new Address('noreply@appquilar.com', 'Appquilar Support'))
            ->to(new Address($email, $name))
            ->subject('Recuperar contraseña')
            ->html($htmlContent);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('emails/forgot_password.html.twig', [
                'resetLink' => $resetLink,
                'name' => $name
            ])
            ->willReturn($htmlContent);

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with($emailToBeSent);

        $this->mailerService->sendForgotPasswordEmail($email, $name, $token);
    }

    public function testDoesNotSendEmailInTestEnvironment(): void
    {
        $testMailerService = new MailerEmailService(
            $this->mailerMock,
            $this->twigMock,
            'test',
            'noreply@appquilar.com',
            'Appquilar Support',
            'devtest@appquilar.com'
        );

        $this->mailerMock->expects($this->never())->method('send');

        $testMailerService->sendForgotPasswordEmail('user@example.com', 'Test User', 'token-123');
    }

    public function testSendForgotPasswordEmailInDevEnvironment(): void
    {
        $testMailerService = new MailerEmailService(
            $this->mailerMock,
            $this->twigMock,
            'dev',
            'noreply@appquilar.com',
            'Appquilar Support',
            'devtest@appquilar.com'
        );
        $email = 'user@example.com';
        $name = 'Test User';
        $token = 'test-token-123';
        $resetLink = 'https://appquilar.com/reset-password?token=' . $token;
        $htmlContent = '<html>Mocked Email Content</html>';
        $emailToBeSent = (new Email())
            ->from(new Address('noreply@appquilar.com', 'Appquilar Support'))
            ->to(new Address('devtest@appquilar.com', $name))
            ->subject('Recuperar contraseña')
            ->html($htmlContent);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('emails/forgot_password.html.twig', [
                'resetLink' => $resetLink,
                'name' => $name
            ])
            ->willReturn($htmlContent);

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with($emailToBeSent);

        $testMailerService->sendForgotPasswordEmail($email, $name, $token);
    }

    public function testSendCompanyUserInvitationEmail(): void
    {
        $email = 'user@example.com';
        $companyName = 'Acme, inc';
        $token = 'test-token-123';
        $htmlContent = '<html>Mocked Email Content</html>';
        $emailToBeSent = (new Email())
            ->from(new Address('noreply@appquilar.com', 'Appquilar Support'))
            ->to(new Address($email))
            ->subject('Activación cuenta para ' . $companyName,)
            ->html($htmlContent);

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with('emails/company_user_invitation.html.twig', [
                'companyName' => $companyName,
                'token' => $token
            ])
            ->willReturn($htmlContent);

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with($emailToBeSent);

        $this->mailerService->sendCompanyUserInvitationEmail($companyName, $email, $token);
    }
}
