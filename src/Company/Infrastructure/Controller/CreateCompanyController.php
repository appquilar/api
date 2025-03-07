<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Command\CreateCompany\CreateCompanyCommand;
use App\Company\Infrastructure\Request\CreateCompanyDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\JsonResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies', name: RoutePermission::COMPANY_CREATE->value, methods: ['POST'])]
class CreateCompanyController
{
    public function __construct(
        private CommandBus $commandBus,
        private JsonResponseService $jsonResponseService,
    ) {
    }

    public function __invoke(CreateCompanyDto $createCompanyDto): JsonResponse
    {
        $this->commandBus->dispatch(
            new CreateCompanyCommand(
                $createCompanyDto->companyId,
                $createCompanyDto->name,
                $createCompanyDto->ownerId,
                $createCompanyDto->description,
                $createCompanyDto->fiscalIdentifier,
                $createCompanyDto->address,
                $createCompanyDto->postalCode,
                $createCompanyDto->city,
                $createCompanyDto->contactEmail,
                $createCompanyDto->phoneNumber
            )
        );

        return $this->jsonResponseService->created();
    }
}
