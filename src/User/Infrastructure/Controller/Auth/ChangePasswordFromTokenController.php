<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller\Auth;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Command\ChangePasswordFromToken\ChangePasswordFromTokenCommand;
use App\User\Infrastructure\Request\ChangePasswordFromTokenDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth/change-password', methods: ['POST'])]
class ChangePasswordFromTokenController
{
    public function __construct(
        private CommandBus      $commandBus,
        private ResponseService $jsonResponseService,
    ) {
    }

    public function __invoke(ChangePasswordFromTokenDto $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new ChangePasswordFromTokenCommand(
                    $request->email,
                    $request->token,
                    $request->password
                )
            );
        } catch (UnauthorizedException $e) {
            return $this->jsonResponseService->unauthorized($e->getMessage());
        }

        return $this->jsonResponseService->noContent();
    }
}
