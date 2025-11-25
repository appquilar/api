<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Controller;

use App\Rent\Application\Query\ListRents\ListRentsQuery;
use App\Rent\Application\Query\ListRents\ListRentsQueryResult;
use App\Rent\Infrastructure\Request\ListRentsDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ListRentsController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    #[Route(path: '/api/rents', name: RoutePermission::RENT_SEARCH->value, methods: ['GET'])]
    public function __invoke(ListRentsDto $dto): JsonResponse
    {
        $query = new ListRentsQuery(
            $dto->productId,
            $dto->startDate,
            $dto->endDate,
            $dto->status,
            $dto->ownerId,
            $dto->page,
            $dto->perPage
        );

        /** @var ListRentsQueryResult $result */
        $result = $this->queryBus->query($query);

        return $this->responseService->okList($result->getResponseData());
    }
}
