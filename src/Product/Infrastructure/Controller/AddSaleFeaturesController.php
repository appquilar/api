<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Command\AddSaleFeatures\AddSaleFeaturesCommand;
use App\Product\Application\Command\RemoveRentalFeatures\RemoveRentalFeaturesCommand;
use App\Product\Infrastructure\Request\AddSaleFeaturesDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{product_id}/sale', name: 'product_add_sale_features', methods: ['POST'])]
class AddSaleFeaturesController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(AddSaleFeaturesDto $request): JsonResponse
    {
        // First, remove any rental features if they exist
        $this->commandBus->dispatch(
            new RemoveRentalFeaturesCommand($request->productId)
        );

        // Then add sale features
        $this->commandBus->dispatch(
            new AddSaleFeaturesCommand(
                $request->productId,
                $request->getPrice(),
                $request->condition,
                $request->yearOfPurchase,
                $request->negotiable,
                $request->additionalInformation
            )
        );

        return $this->responseService->noContent();
    }
}
