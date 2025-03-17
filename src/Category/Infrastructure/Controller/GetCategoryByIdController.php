<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Controller;

use App\Category\Application\Query\GetCategoryById\GetCategoryByIdQuery;
use App\Category\Application\Query\GetCategoryById\GetCategoryByIdQueryResult;
use App\Category\Application\Query\GetCategoryById\GetCategoryBySlugQuery;
use App\Category\Application\Query\GetCategoryById\GetCategoryByISlugQueryResult;
use App\Category\Infrastructure\Request\GetCategoryByIdDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/categories/{category_id}', requirements: ['category_id' => Requirement::UUID_V4], methods: ['GET'])]
class GetCategoryByIdController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(GetCategoryByIdDto $request): JsonResponse
    {
        /** @var GetCategoryByIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetCategoryByIdQuery($request->categoryId)
        );

        return $this->jsonResponseService->ok($queryResult->getCategory());
    }
}
