<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Command\AddUserToCompany\AddUserToCompanyCommand;
use App\Company\Infrastructure\Request\AddUserToCompanyDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\JsonResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}/users', name: RoutePermission::COMPANY_ADD_USER->value, methods: ['POST'])]
class AddUserToCompanyController
{
    public function __construct(
        private CommandBus          $commandBus,
        private JsonResponseService $jsonResponseService,
    ){
    }

    public function __invoke(AddUserToCompanyDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new AddUserToCompanyCommand(
                $request->companyId,
                $request->role,
                $request->userId,
                $request->email
            )
        );
        return $this->jsonResponseService->created();
    }
}
