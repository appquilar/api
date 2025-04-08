<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Command\AddRentalFeatures\AddRentalFeaturesCommand;
use App\Product\Application\Command\RemoveSaleFeatures\RemoveSaleFeaturesCommand;
use App\Product\Infrastructure\Request\AddRentalFeaturesDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{product_id}/rental', name: 'product_add_rental_features', methods: ['POST'])]
class AddRentalFeaturesController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(AddRentalFeaturesDto $request): JsonResponse
    {
        // First, remove any sale features if they exist
        $this->commandBus->dispatch(
            new RemoveSaleFeaturesCommand($request->productId)
        );

        // Then add rental features
        $this->commandBus->dispatch(
            new AddRentalFeaturesCommand(
                $request->productId,
                $request->getDailyPrice(),
                $request->getHourlyPrice(),
                $request->getWeeklyPrice(),
                $request->getMonthlyPrice(),
                $request->getDeposit(),
                $request->alwaysAvailable,
                $request->availabilityPeriods,
                $request->includesWeekends
            )
        );

        return $this->responseService->noContent();
    }
}
