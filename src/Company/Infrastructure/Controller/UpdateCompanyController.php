<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Command\UpdateCompany\UpdateCompanyCommand;
use App\Company\Infrastructure\Request\UpdateCompanyDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}', name: RoutePermission::COMPANY_UPDATE->name, methods: ['PATCH'])]
class UpdateCompanyController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService,
    ) {
    }

    public function __invoke(UpdateCompanyDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateCompanyCommand(
                $request->companyId,
                $request->name,
                $request->slug,
                $request->description,
                $request->fiscalIdentifier,
                $request->address,
                $request->postalCode,
                $request->city,
                $request->contactEmail,
                $request->phoneNumber
            )
        );

        return $this->responseService->noContent();
    }
}
