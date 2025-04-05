<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Controller;

use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Security\RoutePermission;
use App\Shared\Infrastructure\Service\ResponseService;
use App\Site\Application\Query\ListSites\ListSitesQuery;
use App\Site\Application\Query\ListSites\ListSitesQueryResult;
use App\Site\Infrastructure\Request\FindAllSitesDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sites', name: RoutePermission::SITE_LIST_ALL->value, methods: ['GET'])]
class FindAllSitesController
{
    public function __construct(
        private QueryBus        $queryBus,
        private ResponseService $jsonResponseService,
    ) {
    }

    public function __invoke(FindAllSitesDto $request): JsonResponse
    {
        /** @var ListSitesQueryResult $queryResult */
        $queryResult = $this->queryBus->query(new ListSitesQuery());

        return $this->jsonResponseService->ok($queryResult->getSites());
    }
}
