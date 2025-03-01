<?php

declare(strict_types=1);

namespace App\User\Application\Command\Logout;

use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Context\UserGranted;
use App\User\Application\Service\AuthTokenServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: LogoutCommand::class)]
class LogoutCommandHandler implements CommandHandler
{
    public function __construct(
        private AuthTokenServiceInterface $authTokenService,
        private UserGranted $userGranted
    ) {
    }

    public function __invoke(LogoutCommand|Command $command): void
    {
        $this->authTokenService->revoke($this->userGranted->getToken());
    }
}
