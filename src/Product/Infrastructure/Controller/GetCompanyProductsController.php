<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Query\GetCompanyProducts\GetCompanyProductsQuery;
use App\Product\Application\Query\GetCompanyProducts\GetCompanyProductsQueryResult;
use App\Product\Infrastructure\Request\GetCompanyProductsDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}/products', methods: ['GET'])]
class GetCompanyProductsController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(GetCompanyProductsDto $request): JsonResponse
    {
        /** @var GetCompanyProductsQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetCompanyProductsQuery(
                $request->companyId,
                $request->page,
                $request->perPage
            )
        );

        return $this->responseService->okList($queryResult->getResponseData());
    }
}
