<?php

declare(strict_types=1);

namespace App\Notification\Application\Listener;

use App\Company\Application\Event\CompanyUserCreated;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Notification\Application\Service\EmailServiceInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: CompanyUserCreated::class)]
class OnCompanyUserCreatedSendInvitationListener
{
    public function __construct(
        private EmailServiceInterface $emailService,
        private CompanyRepositoryInterface $companyRepository,
    ) {
    }

    public function __invoke(CompanyUserCreated $event): void
    {
        if ($event->isOwner()) {
            return;
        }

        $company = $this->companyRepository->findById($event->getCompanyId());

        $this->emailService->sendCompanyUserInvitationEmail(
            $company->getId(),
            $company->getName(),
            $event->getEmail(),
            $event->getToken()
        );
    }
}
