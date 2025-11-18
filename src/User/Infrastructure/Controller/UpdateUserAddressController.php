<?php declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Command\UpdateUserAddress\UpdateUserAddressCommand;
use App\User\Infrastructure\Request\UpdateUserAddressDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users/{user_id}/address', name: RoutePermission::USER_UPDATE_ADDRESS->value, methods: ['PATCH'])]
class UpdateUserAddressController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(UpdateUserAddressDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateUserAddressCommand(
                $request->userId,
                $request->address?->toAddress(),
                $request->location?->toGeoLocation()
            )
        );

        return $this->jsonResponseService->noContent();
    }

}
