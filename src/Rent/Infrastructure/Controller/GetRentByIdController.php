<?php declare(strict_types=1);

namespace App\Rent\Infrastructure\Controller;

use App\Rent\Application\Query\GetRentById\GetRentByIdQuery;
use App\Rent\Infrastructure\Request\GetRentByIdDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetRentByIdController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $responseService
    ) {
    }

    #[Route(path: '/api/rents/{rent_id}', name: RoutePermission::RENT_GET_BY_ID->value, methods: ['GET'])]
    public function __invoke(GetRentByIdDto $dto): JsonResponse
    {
        $query = new GetRentByIdQuery(
            $dto->rentId
        );

        $result = $this->queryBus->query($query);

        return $this->responseService->ok($result->getRent());
    }
}
