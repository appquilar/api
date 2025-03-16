<?php

declare(strict_types=1);

namespace App\Company\Application\Command\CreateCompany;

use App\Company\Application\Event\CompanyCreated;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Entity\Company;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateCompanyCommand::class)]
class CreateCompanyCommandHandler implements CommandHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private SlugifyServiceInterface $slugifyService,
        private EventDispatcherInterface   $eventDispatcher,
        private readonly UserGranted $userGranted
    ) {
    }

    public function __invoke(CreateCompanyCommand|Command $command): void
    {
        $slug = $this->slugifyService->generate($command->getName());
        $this->slugifyService->validateSlugIsUnique($slug, $this->companyRepository);

        $company = new Company(
            $command->getCompanyId(),
            $command->getName(),
            $slug,
            $command->getDescription(),
            $command->getFiscalIdentifier(),
            $command->getAddress(),
            $command->getPostalCode(),
            $command->getCity(),
            $command->getContactEmail(),
            $command->getPhoneNumber()
        );

        $this->companyRepository->save($company);

        $this->eventDispatcher->dispatch(
            new CompanyCreated(
                $company->getId(),
                $command->getOwnerId(),
                $this->userGranted->getUser()->getEmail()
            )
        );
    }
}
