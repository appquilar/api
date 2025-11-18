<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Query\GetProductById\GetProductByIdQuery;
use App\Product\Application\Query\GetProductById\GetProductByIdQueryResult;
use App\Product\Infrastructure\Request\GetProductByIdDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route(
    path: '/api/products/{product_id}',
    name: RoutePermission::PRODUCT_GET_BY_ID->name,
    requirements: ['product_id' => Requirement::UUID_V4],
    methods: ['GET']
)]
class GetProductByIdController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(GetProductByIdDto $request): JsonResponse
    {
        /** @var GetProductByIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetProductByIdQuery($request->productId)
        );

        return $this->responseService->ok($queryResult->getProduct());
    }
}
