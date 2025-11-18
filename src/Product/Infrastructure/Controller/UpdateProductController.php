<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Product\Infrastructure\Request\UpdateProductDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{product_id}', name: RoutePermission::PRODUCT_UPDATE->name, methods: ['PATCH'])]
class UpdateProductController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(UpdateProductDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new UpdateProductCommand(
                $request->productId,
                $request->name,
                $request->internalId,
                $request->description,
                $request->deposit,
                $request->tiers,
                $request->quantity,
                $request->categoryId,
                $request->imageIds,
            )
        );

        return $this->responseService->noContent();
    }
}
