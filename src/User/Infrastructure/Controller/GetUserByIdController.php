<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Query\GetUserById\GetUserByIdQuery;
use App\User\Application\Query\GetUserById\GetUserByIdQueryResult;
use App\User\Infrastructure\Request\GetUserByIdDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users/{user_id}', name: RoutePermission::USER_GET_BY_ID->value, methods: ['GET'])]
class GetUserByIdController
{
    public function __construct(
        private QueryBus        $queryBus,
        private ResponseService $jsonResponseService,
    ) {
    }

    public function __invoke(GetUserByIdDto $request): JsonResponse
    {
        /** @var GetUserByIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(new GetUserByIdQuery($request->userId));

        return $this->jsonResponseService->ok($queryResult->getUser());
    }
}
