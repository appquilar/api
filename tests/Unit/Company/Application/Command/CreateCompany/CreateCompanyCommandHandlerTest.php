<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\CreateCompany;

use App\Company\Application\Command\CreateCompany\CreateCompanyCommand;
use App\Company\Application\Command\CreateCompany\CreateCompanyCommandHandler;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Entity\Company;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Uid\Uuid;

class CreateCompanyCommandHandlerTest extends UnitTestCase
{
    public function testHandleCreateCompanySuccessfully(): void
    {
        $companyId = Uuid::v4();
        $ownerId = Uuid::v4();
        $name = "Acme Inc.";
        $description = "An innovative company.";

        $repositoryMock = $this->createMock(CompanyRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Company $company) use ($companyId, $ownerId, $name, $description) {
                return $company->getId() === $companyId &&
                    $company->getOwnerId() === $ownerId &&
                    $company->getName() === $name &&
                    $company->getDescription() === $description;
            }));

        $commandHandler = new CreateCompanyCommandHandler($repositoryMock);
        $command = new CreateCompanyCommand($companyId, $name, $ownerId, $description);

        $commandHandler($command);
    }
}
