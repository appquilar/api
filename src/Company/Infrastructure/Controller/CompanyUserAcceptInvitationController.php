<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Command\CompanyUserAcceptInvitation\CompanyUserAcceptInvitationCommand;
use App\Company\Infrastructure\Request\CompanyUserAcceptInvitationDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\JsonResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}/invitations/{token}/accept', name: RoutePermission::COMPANY_USER_ACCEPTS_INVITATION->value, methods: ['POST'])]
class CompanyUserAcceptInvitationController
{
    public function __construct(
        private CommandBus $commandBus,
        private JsonResponseService $jsonResponseService,
    ) {
    }

    public function __invoke(CompanyUserAcceptInvitationDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new CompanyUserAcceptInvitationCommand(
                $request->companyId,
                $request->token,
                $request->email,
                $request->password,
        ));

        return $this->jsonResponseService->noContent();
    }
}
