<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Application\Listener;

use App\Company\Application\Event\CompanyUserCreated;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Notification\Application\Listener\OnCompanyUserCreatedSendInvitationListener;
use App\Notification\Application\Service\EmailServiceInterface;
use App\Tests\Factories\Company\Domain\Entity\CompanyFactory;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Uid\Uuid;

class OnCompanyUserCreatedSendInvitationListenerTest extends UnitTestCase
{
    private $emailServiceMock;
    private $companyRepositoryMock;
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailServiceMock = $this->createMock(EmailServiceInterface::class);
        $this->companyRepositoryMock = $this->createMock(CompanyRepositoryInterface::class);
        $this->listener = new OnCompanyUserCreatedSendInvitationListener(
            $this->emailServiceMock,
            $this->companyRepositoryMock
        );
    }
    
    public function testOwnerCreated(): void
    {
        $event = new CompanyUserCreated(Uuid::v4(), 'example@test.com', true);
        $this->emailServiceMock->expects($this->never())
            ->method('sendCompanyUserInvitationEmail');
        
        $this->listener->__invoke($event);
    }
    
    public function testContributorCreated(): void
    {
        $companyId = Uuid::v4();
        $company = CompanyFactory::createOne(['companyId' => $companyId]);
        $event = new CompanyUserCreated($companyId, 'example@test.com', false, '1234');
        $this->companyRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);
        $this->emailServiceMock->expects($this->once())
            ->method('sendCompanyUserInvitationEmail')
            ->with(
                $company->getId(),
                $company->getName(),
                $event->getEmail(),
                $event->getToken()
            );

        $this->listener->__invoke($event);
    }
}
