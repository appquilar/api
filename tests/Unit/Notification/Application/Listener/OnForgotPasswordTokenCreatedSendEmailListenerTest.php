<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Application\Listener;

use App\Notification\Application\Listener\OnForgotPasswordTokenCreatedSendEmailListener;
use App\Notification\Application\Service\EmailServiceInterface;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Event\ForgotPasswordTokenCreated;

class OnForgotPasswordTokenCreatedSendEmailListenerTest extends UnitTestCase
{
    private EmailServiceInterface $emailService;
    private OnForgotPasswordTokenCreatedSendEmailListener $listener;

    protected function setUp(): void
    {
        $this->emailService = $this->createMock(EmailServiceInterface::class);
        $this->listener = new OnForgotPasswordTokenCreatedSendEmailListener($this->emailService);
    }

    public function testHandleForgotPasswordTokenCreatedSuccessfully(): void
    {
        $event = new ForgotPasswordTokenCreated(
            'John Doe',
            'user@example.com',
            'fake-token'
        );

        $this->emailService->expects($this->once())
            ->method('sendForgotPasswordEmail')
            ->with(
                'user@example.com',
                'John Doe',
                'fake-token'
            );

        $this->listener->__invoke($event);
    }
}
