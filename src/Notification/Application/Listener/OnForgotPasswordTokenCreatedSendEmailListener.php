<?php

declare(strict_types=1);

namespace App\Notification\Application\Listener;

use App\Notification\Application\Service\EmailServiceInterface;
use App\User\Application\Event\ForgotPasswordTokenCreated;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ForgotPasswordTokenCreated::class)]
class OnForgotPasswordTokenCreatedSendEmailListener
{
    public function __construct(
        private EmailServiceInterface $emailService
    ) {
    }

    public function __invoke(ForgotPasswordTokenCreated $event): void
    {
        $this->emailService->sendForgotPasswordEmail($event->getEmail(), $event->getName(), $event->getToken());
    }
}
