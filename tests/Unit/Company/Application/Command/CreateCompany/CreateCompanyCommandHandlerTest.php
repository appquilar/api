<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\CreateCompany;

use App\Company\Application\Command\CreateCompany\CreateCompanyCommand;
use App\Company\Application\Command\CreateCompany\CreateCompanyCommandHandler;
use App\Company\Application\Event\CompanyCreated;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Entity\Company;
use App\Shared\Application\Context\UserGranted;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use App\User\Domain\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class CreateCompanyCommandHandlerTest extends UnitTestCase
{
    public function testHandleCreateCompanySuccessfully(): void
    {
        $companyId = Uuid::v4();
        $ownerId = Uuid::v4();
        $name = "Acme Inc.";
        $description = "An innovative company.";
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $ownerId]);

        $repositoryMock = $this->createMock(CompanyRepositoryInterface::class);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $userGranted = $this->createMock(UserGranted::class);

        $repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Company $company) use ($companyId, $ownerId, $name, $description) {
                return $company->getId() === $companyId &&
                    $company->getName() === $name &&
                    $company->getDescription() === $description;
            }));

        $userGranted->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(
                new CompanyCreated($companyId, $ownerId, $user->getEmail())
            );


        $commandHandler = new CreateCompanyCommandHandler(
            $repositoryMock,
            $eventDispatcherMock,
            $userGranted
        );
        $command = new CreateCompanyCommand($companyId, $name, $ownerId, $description);

        $commandHandler($command);
    }
}
