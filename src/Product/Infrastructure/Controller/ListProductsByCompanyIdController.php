<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\Query\ListProductsByOwner\ListProductsByOwnerQuery;
use App\Product\Application\Query\ListProductsByOwner\ListProductsByOwnerQueryResult;
use App\Product\Domain\Enum\ProductOwner;
use App\Product\Infrastructure\Request\ListProductsByOwnerDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{owner_id}/products', methods: ['GET'])]
class ListProductsByCompanyIdController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    public function __invoke(ListProductsByOwnerDto $request): JsonResponse
    {
        /** @var ListProductsByOwnerQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new ListProductsByOwnerQuery(
                $request->ownerId,
                ProductOwner::COMPANY,
                $request->page,
                $request->perPage
            )
        );

        return $this->responseService->ok($queryResult->getResponseData());
    }
}
