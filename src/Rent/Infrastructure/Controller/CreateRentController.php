<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Controller;

use App\Rent\Application\Command\CreateRent\CreateRentCommand;
use App\Rent\Infrastructure\Request\CreateRentDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/rents', name: RoutePermission::RENT_CREATE->name, methods: ['POST'])]
class CreateRentController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService,
    ) {
    }

    public function __invoke(CreateRentDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new CreateRentCommand(
                $request->rentId,
                $request->productId,
                $request->renterId,
                \DateTime::createFromImmutable($request->startDate),
                \DateTime::createFromImmutable($request->endDate),
                $request->deposit->toMoney(),
                $request->price->toMoney()
            )
        );

        return $this->responseService->created();
    }
}
