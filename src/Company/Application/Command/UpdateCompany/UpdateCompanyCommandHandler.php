<?php

declare(strict_types=1);

namespace App\Company\Application\Command\UpdateCompany;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Service\SlugifyServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateCompanyCommand::class)]
class UpdateCompanyCommandHandler implements CommandHandler
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private SlugifyServiceInterface $slugifyService,
        private UserGranted $userGranted
    ) {
    }

    public function __invoke(UpdateCompanyCommand|Command $command): void
    {
        if (
            !$this->userGranted->isAdmin() &&
            !$this->userGranted->isAdminAtThisCompany($command->getCompanyId())
        ) {
            throw new UnauthorizedException();
        }

        $company = $this->companyRepository->findById($command->getCompanyId());

        if ($company === null) {
            throw new EntityNotFoundException($command->getCompanyId());
        }

        $slug = $this->slugifyService->generate($command->getSlug());
        $this->slugifyService->validateSlugIsUnique($slug, $this->companyRepository, $company->getId());

        $company->update(
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
    }
}
