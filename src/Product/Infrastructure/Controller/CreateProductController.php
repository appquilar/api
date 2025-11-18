<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Command\CreateProduct\CreateProductCommand;
use App\Product\Infrastructure\Request\CreateProductDto;
use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products', name: RoutePermission::PRODUCT_CREATE->name, methods: ['POST'])]
class CreateProductController
{
    public function __construct(
        private CommandBus $commandBus,
        private ResponseService $responseService,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CreateProductDto $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new CreateProductCommand(
                $request->productId,
                $request->name,
                $request->internalId,
                $request->description,
                $request->deposit,
                $request->tiers,
                $request->quantity,
                $request->categoryId,
                $request->imageIds,
                $request->companyId
            )
        );

        return $this->responseService->created();
    }
}
