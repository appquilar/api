<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Command\RemoveCompanyUser\RemoveCompanyUserCommand;
use App\Company\Infrastructure\Request\RemoveCompanyUserDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}/users/{user_id}', name: RoutePermission::COMPANY_REMOVE_USER->value, methods: ['DELETE'])]
class RemoveCompanyUserController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(RemoveCompanyUserDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new RemoveCompanyUserCommand(
                $request->companyId,
                $request->userId
            )
        );

        return $this->jsonResponseService->noContent();
    }
}
