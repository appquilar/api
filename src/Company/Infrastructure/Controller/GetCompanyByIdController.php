<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQuery;
use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQueryResult;
use App\Company\Infrastructure\Request\GetCompanyByIdDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{company_id}', methods: ['GET'])]
class GetCompanyByIdController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(GetCompanyByIdDto $request): JsonResponse
    {
        /** @var GetCompanyByIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(new GetCompanyByIdQuery($request->companyId));

        return $this->jsonResponseService->ok($queryResult->getCompany());
    }
}
