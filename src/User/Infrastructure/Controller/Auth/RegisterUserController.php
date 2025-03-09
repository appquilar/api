<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller\Auth;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Service\JsonResponseService;
use App\User\Application\Command\RegisterUser\RegisterUserCommand;
use App\User\Infrastructure\Request\RegisterUserDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth/register', methods: ['POST'])]
class RegisterUserController
{
    public function __construct(
        private CommandBus $commandBus,
        private JsonResponseService $jsonResponseService
    ) {
    }

    public function __invoke(RegisterUserDto $dto): JsonResponse
    {
        $this->commandBus->dispatch(
            new RegisterUserCommand(
                $dto->userId,
                strtolower($dto->email),
                $dto->password
            )
        );

        return $this->jsonResponseService->created();
    }
}
