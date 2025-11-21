<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Query\ListProductsByCategoryId\ListProductsByCategoryIdQuery;
use App\Product\Application\Query\ListProductsByCategoryId\ListProductsByCategoryIdQueryResult;
use App\Product\Infrastructure\Request\ListProductsByCategoryTreeDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories/{category_id}/products', methods: ['GET'])]
class ListProductsByCategoriesTreeController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(ListProductsByCategoryTreeDto $request): JsonResponse
    {
        /** @var ListProductsByCategoryIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new ListProductsByCategoryIdQuery(
                $request->categoryId,
                $request->page,
                $request->perPage
            )
        );

        return $this->responseService->ok($queryResult->getResponseData());
    }
}
