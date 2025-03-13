<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Command\ChangePassword\ChangePasswordCommand;
use App\User\Infrastructure\Request\ChangePasswordDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users/{user_id}/change-password', name: RoutePermission::USER_CHANGE_PASSWORD->value, methods: ['PATCH'])]
class ChangePasswordController
{
    public function __construct(
        private CommandBus          $commandBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(ChangePasswordDto $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new ChangePasswordCommand(
                    $request->newPassword,
                    $request->oldPassword
                )
            );
        } catch (UnauthorizedException $e) {
            return $this->jsonResponseService->unauthorized($e->getMessage());
        }

        return $this->jsonResponseService->noContent();
    }
}
