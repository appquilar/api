<?php

declare(strict_types=1);

namespace App\User\Application\Command\Logout;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Context\UserGranted;
use App\User\Application\Service\AuthTokenServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: LogoutCommand::class)]
class LogoutCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private AuthTokenServiceInterface $authTokenService,
        private UserGranted $userGranted
    ) {
    }

    public function __invoke(LogoutCommand|CommandInterface $command): void
    {
        $this->authTokenService->revoke($this->userGranted->getToken());
    }
}
