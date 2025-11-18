<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Query\GetProductBySlug\GetProductBySlugQuery;
use App\Product\Application\Query\GetProductBySlug\GetProductBySlugQueryResult;
use App\Product\Infrastructure\Request\GetProductBySlugDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{slug}', methods: ['GET'])]
class GetProductBySlugController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(GetProductBySlugDto $request): JsonResponse
    {
        /** @var GetProductBySlugQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetProductBySlugQuery($request->slug)
        );

        return $this->responseService->ok($queryResult->getProduct());
    }
}
