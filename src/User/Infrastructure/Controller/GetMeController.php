<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Query\GetUserById\GetUserByIdQuery;
use App\User\Application\Query\GetUserById\GetUserByIdQueryResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/me', methods: ['GET'])]
class GetMeController
{
    public function __construct(
        private QueryBus        $queryBus,
        private ResponseService $jsonResponseService,
        private UserGranted     $userGranted,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var GetUserByIdQueryResult $queryResult */
        $queryResult = $this->queryBus->query(new GetUserByIdQuery($this->userGranted->getUser()->getId()));

        return $this->jsonResponseService->ok($queryResult->getUser());
    }
}
