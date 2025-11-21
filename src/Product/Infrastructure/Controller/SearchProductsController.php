<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Query\SearchProducts\SearchProductsQuery;
use App\Product\Application\Query\SearchProducts\SearchProductsQueryResult;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Product\Infrastructure\Request\SearchProductsDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/search', methods: ['GET'], priority: 10)]
class SearchProductsController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(SearchProductsDto $request): JsonResponse
    {
        /** @var SearchProductsQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new SearchProductsQuery(
                $request->text,
                $request->latitude,
                $request->longitude,
                $request->radius,
                PublicationStatus::published(),
                $request->categories,
                $request->page,
                $request->perPage
            )
        );

        return $this->responseService->okList(
            $queryResult->getResponseData()
        );
    }
}
