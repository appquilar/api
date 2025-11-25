<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Controller;

use App\Rent\Application\Command\UpdateRentStatus\UpdateRentStatusCommand;
use App\Rent\Infrastructure\Request\UpdateRentStatusDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/rents/{rent_id}/status', name: RoutePermission::RENT_STATUS_UPDATE->name, methods: ['PATCH'])]
class UpdateRentStatusController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService,
    ) {
    }

    public function __invoke(UpdateRentStatusDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateRentStatusCommand(
                $request->rentId,
                $request->rentStatus,
            )
        );

        return $this->responseService->noContent();
    }
}
