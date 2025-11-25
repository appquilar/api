<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Controller;

use App\Rent\Application\Command\UpdateRent\UpdateRentCommand;
use App\Rent\Infrastructure\Request\UpdateRentDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/rents/{rent_id}', name: RoutePermission::RENT_UPDATE->name, methods: ['PATCH'])]
class UpdateRentController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService,
    ) {
    }

    public function __invoke(UpdateRentDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateRentCommand(
                $request->rentId,
                \DateTime::createFromImmutable($request->startDate),
                \DateTime::createFromImmutable($request->endDate),
                $request->deposit->toMoney(),
                $request->price->toMoney(),
                $request->depositReturned?->toMoney(),
            )
        );

        return $this->responseService->noContent();
    }
}
