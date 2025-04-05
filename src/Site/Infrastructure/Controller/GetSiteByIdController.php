<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Controller;

use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use App\Site\Application\Query\GetSiteById\GetSiteByIdQuery;
use App\Site\Application\Query\GetSiteById\GetSiteByIdQueryResult;
use App\Site\Infrastructure\Request\GetSiteByIdDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sites/{site_id}', methods: ['GET'])]
class GetSiteByIdController
{
    public function __construct(
        private QueryBus        $queryBus,
        private ResponseService $jsonResponseService,
    ) {
    }

    public function __invoke(GetSiteByIdDto $request): JsonResponse
    {
        /** @var GetSiteByIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(new GetSiteByIdQuery($request->siteId));

        return $this->jsonResponseService->ok($queryResult->getSite());
    }
}
