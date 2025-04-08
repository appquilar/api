<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Command\PublishProduct\PublishProductCommand;
use App\Product\Infrastructure\Request\ProductByIdDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{product_id}/publish', name: 'product_publish', methods: ['PATCH'])]
class PublishProductController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(ProductByIdDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new PublishProductCommand($request->productId)
        );

        return $this->responseService->noContent();
    }
}
