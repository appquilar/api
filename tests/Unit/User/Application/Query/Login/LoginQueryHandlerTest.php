<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Query\Login;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Query\Login\LoginQuery;
use App\User\Application\Query\Login\LoginQueryHandler;
use App\User\Application\Query\Login\LoginQueryResult;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Hautelook\Phpass\PasswordHash;
use Symfony\Component\Uid\Uuid;

class LoginQueryHandlerTest extends UnitTestCase
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasher $passwordHasher;
    private AuthTokenServiceInterface $authTokenService;
    private LoginQueryHandler $queryHandler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasher::class);
        $this->authTokenService = $this->createMock(AuthTokenServiceInterface::class);
        $this->queryHandler = new LoginQueryHandler($this->userRepository, $this->passwordHasher, $this->authTokenService);
    }

    public function testLoginWithSymfonyUser(): void
    {
        $user = new User(
            Uuid::v4(),
            'symfonyuser@example.com',
            password_hash('SymfonyPassword123', PASSWORD_BCRYPT)
        );

        $this->userRepository->expects($this->once())
            ->method('findByEmail')
            ->with('symfonyuser@example.com')
            ->willReturn($user);

        $this->passwordHasher->expects($this->once())
            ->method('verifyPassword')
            ->with('SymfonyPassword123', $user->getPassword())
            ->willReturn(true);

        $this->authTokenService->expects($this->once())
            ->method('encode')
            ->willReturn('fake_jwt_token');

        $query = new LoginQuery('symfonyuser@example.com', 'SymfonyPassword123');
        $result = $this->queryHandler->__invoke($query);

        $this->assertInstanceOf(LoginQueryResult::class, $result);
        $this->assertEquals('fake_jwt_token', $result->getToken());
    }

    public function testLoginFailsWithInvalidCredentials(): void
    {
        $this->userRepository->expects($this->once())
            ->method('findByEmail')
            ->with('invalid@example.com')
            ->willReturn(null);

        $this->expectException(UnauthorizedException::class);

        $query = new LoginQuery('invalid@example.com', 'WrongPass');
        $this->queryHandler->__invoke($query);
    }
}
