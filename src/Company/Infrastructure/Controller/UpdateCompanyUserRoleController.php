<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Command\UpdateCompanyUserRole\UpdateCompanyUserRoleCommand;
use App\Company\Infrastructure\Request\UpdateCompanyUserRoleDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\JsonResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}/users/{user_id}', name: RoutePermission::COMPANY_UPDATE_USER_ROLE->value, methods: ['PATCH'])]
class UpdateCompanyUserRoleController
{
    public function __construct(
        private CommandBus $commandBus,
        private JsonResponseService $jsonResponseService
    ) {
    }

    public function __invoke(UpdateCompanyUserRoleDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateCompanyUserRoleCommand(
                $request->companyId,
                $request->userId,
                $request->role
            )
        );

        return $this->jsonResponseService->noContent();
    }
}
