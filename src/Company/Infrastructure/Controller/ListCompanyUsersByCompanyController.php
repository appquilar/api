<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Query\GetCompanyUsers\GetCompanyUsersQuery;
use App\Company\Application\Query\GetCompanyUsers\GetCompanyUsersQueryResult;
use App\Company\Infrastructure\Request\GetCompanyUsersByCompanyIdDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\JsonResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}/users', name: RoutePermission::COMPANY_LIST_USERS->value, methods: ['GET'])]
class ListCompanyUsersByCompanyController
{
    public function __construct(
        private QueryBus $queryBus,
        private JsonResponseService $jsonResponseService
    ) {
    }

    public function __invoke(GetCompanyUsersByCompanyIdDto $request): JsonResponse
    {
        /** @var GetCompanyUsersQueryResult $queryResult */
        $queryResult = $this->queryBus->query(
            new GetCompanyUsersQuery(
                $request->companyId,
                $request->page,
                $request->perPage
            )
        );

        return $this->jsonResponseService->okList($queryResult->getResponseData());
    }
}
