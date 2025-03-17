<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Controller;

use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQuery;
use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQueryResult;
use App\Company\Application\Query\GetCompanyBySlug\GetCompanyBySlugQuery;
use App\Company\Application\Query\GetCompanyBySlug\GetCompanyBySlugQueryResult;
use App\Company\Infrastructure\Request\GetCompanyByIdDto;
use App\Company\Infrastructure\Request\GetCompanyBySlugDto;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies/{slug}', methods: ['GET'])]
class GetCompanyBySlugController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(GetCompanyBySlugDto $request): JsonResponse
    {
        /** @var GetCompanyBySlugQueryResult $queryResult */
        $queryResult = $this->queryBus->query(new GetCompanyBySlugQuery($request->slug));

        return $this->jsonResponseService->ok($queryResult->getCompany());
    }
}
