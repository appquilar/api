<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Controller;

use App\Category\Application\Query\GetCategoryBySlug\GetCategoryBySlugQuery;
use App\Category\Application\Query\GetCategoryBySlug\GetCategoryBySlugQueryResult;
use App\Category\Infrastructure\Request\GetCategoryBySlugDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories/{slug}', methods: ['GET'])]
class GetCategoryBySlugController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(GetCategoryBySlugDto $request): JsonResponse
    {
        /** @var GetCategoryBySlugQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetCategoryBySlugQuery($request->slug)
        );

        return $this->jsonResponseService->ok($queryResult->getCategory());
    }
}
