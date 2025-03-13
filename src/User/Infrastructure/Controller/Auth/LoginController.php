<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller\Auth;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Query\QueryBus;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Query\Login\LoginQuery;
use App\User\Application\Query\Login\LoginQueryResult;
use App\User\Infrastructure\Request\LoginUserDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth/login', methods: ['POST'])]
class LoginController
{
    public function __construct(
        private QueryBus $queryBus,
        private ResponseService $jsonResponseService
    ) {
    }

    public function __invoke(LoginUserDto $dto): JsonResponse
    {
        try {
            /** @var LoginQueryResult $queryResult */
            $queryResult = $this->queryBus->query(
                new LoginQuery($dto->email, $dto->password)
            );
        } catch (UnauthorizedException $e) {
            return $this->jsonResponseService->unauthorized();
        }

        return $this->jsonResponseService->ok(['token' => $queryResult->getToken()]);
    }
}
