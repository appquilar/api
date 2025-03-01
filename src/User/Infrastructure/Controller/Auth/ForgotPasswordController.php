<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller\Auth;

use App\Shared\Application\Command\CommandBus;
use App\Shared\Infrastructure\Service\JsonResponseService;
use App\User\Application\Command\ForgotPassword\ForgotPasswordCommand;
use App\User\Infrastructure\Request\ForgotPasswordDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth/forgot-password', methods: ['POST'])]
class ForgotPasswordController
{
    public function __construct(
        private CommandBus          $commandBus,
        private JsonResponseService $jsonResponseService,
        private string $env,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ForgotPasswordDto $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(
                new ForgotPasswordCommand($request->email)
            );
        } catch (\Exception $e) {
            if ($this->env === 'prod') {
                error_log($e->getMessage());
            } else {
                throw $e;
            }
        } finally {
            return $this->jsonResponseService->ok();
        }
    }
}
