<?php declare(strict_types=1);

namespace App\Category\Infrastructure\Controller;

use App\Category\Application\Query\GetCategoryBreadcrumbs\GetCategoryBreadcrumbsQuery;
use App\Category\Application\Query\GetCategoryBreadcrumbs\GetCategoryBreadcrumbsQueryResult;
use App\Category\Infrastructure\Request\GetCategoryByIdDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories/{category_id}/breadcrumbs', methods: ['GET'])]
class ListCategoryBreadcrumbsController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(GetCategoryByIdDto $request): JsonResponse
    {
        /** @var GetCategoryBreadcrumbsQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetCategoryBreadcrumbsQuery($request->categoryId)
        );

        return $this->jsonResponseService->okList(['data' => $queryResult->getBreadcrumbs()]);
    }
}
