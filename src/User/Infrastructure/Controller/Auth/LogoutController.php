<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller\Auth;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Command\Logout\LogoutCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth/logout', methods: ['POST'])]
class LogoutController
{
    public function __construct(
        private CommandBus      $commandBus,
        private ResponseService $jsonResponseService,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->commandBus->dispatch(new LogoutCommand());

        return $this->jsonResponseService->ok();
    }

}
