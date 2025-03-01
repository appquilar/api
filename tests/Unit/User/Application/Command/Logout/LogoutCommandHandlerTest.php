<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Command\Logout;

use App\Shared\Application\Context\UserGranted;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Command\Logout\LogoutCommand;
use App\User\Application\Command\Logout\LogoutCommandHandler;
use App\User\Application\Service\AuthTokenServiceInterface;

class LogoutCommandHandlerTest extends UnitTestCase
{
    public function testHandleRevokesToken(): void
    {
        $token = 'test-token';

        $authTokenService = $this->createMock(AuthTokenServiceInterface::class);
        $userGranted = $this->createMock(UserGranted::class);
        $authTokenService->expects($this->once())
            ->method('revoke')
            ->with($this->equalTo($token));
        $userGranted->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $handler = new LogoutCommandHandler($authTokenService, $userGranted);
        $handler(new LogoutCommand());
    }
}
