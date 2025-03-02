<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Service\JsonResponseService;
use App\User\Application\Command\UpdateUser\UpdateUserCommand;
use App\User\Infrastructure\Request\UpdateUserDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users/{user_id}', name: 'users_update_user', methods: ['PATCH'])]
class UpdateUserController
{
    public function __construct(
        private CommandBus $commandBus,
        private JsonResponseService $jsonResponseService
    ) {
    }

    public function __invoke(UpdateUserDto $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new UpdateUserCommand(
                    $request->userId,
                    $request->firstName,
                    $request->lastName,
                    $request->email,
                    $request->roles
                )
            );
        } catch (BadRequestException $e) {
            return $this->jsonResponseService->badRequest($e->getMessage());
        } catch (UnauthorizedException $e) {
            return $this->jsonResponseService->unauthorized($e->getMessage());
        }

        return $this->jsonResponseService->noContent();
    }
}
