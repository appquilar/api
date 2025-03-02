<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Query\GetUserById;

use App\Shared\Application\Context\UserGranted;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use App\User\Application\Query\GetUserById\GetUserByIdQuery;
use App\User\Application\Query\GetUserById\GetUserByIdQueryHandler;
use App\User\Application\Query\GetUserById\GetUserByIdQueryResult;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Transformer\UserTransformer;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

class GetUserByIdQueryHandlerTest extends UnitTestCase
{
    private UserGranted $userGrantedMock;
    private UserTransformer $userTransformerMock;
    private UserRepositoryInterface $userRepositoryMock;
    private GetUserByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userGrantedMock = $this->createMock(UserGranted::class);
        $this->userTransformerMock = $this->createMock(UserTransformer::class);
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->handler = new GetUserByIdQueryHandler(
            $this->userRepositoryMock,
            $this->userGrantedMock,
            $this->userTransformerMock
        );
    }

    public function testHandleReturnsAuthenticatedUserWhenRequestingOwnProfile(): void
    {
        $userId = Uuid::v4();
        $email = 'test@example.com';
        /** @var User $user */
        $user = UserFactory::createOne(['userId' => $userId, 'email' => $email]);
        $userTransformed = [
            'user_id' => $user->getId()->toString(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];

        $this->userGrantedMock->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);
        $this->userTransformerMock->expects($this->once())
            ->method('transform')
            ->with($user)
            ->willReturn($userTransformed);

        $transformer = $this->createMock(UserTransformer::class);
        $transformer->method('transform')
            ->with($user)
            ->willReturn([
            'id' => $userId->toRfc4122(),
            'email' => 'test@example.com',
            'roles' => ['ROLE_USER'],
        ]);

        $query = new GetUserByIdQuery($userId);

        $result = $this->handler->__invoke($query);

        $this->assertInstanceOf(GetUserByIdQueryResult::class, $result);
        $this->assertEquals($userId->toString(), $result->getUser()['user_id']);
        $this->assertEquals($email, $result->getUser()['email']);
    }

    public function testHandleReturnsUserFromRepositoryWhenRequestingAnotherUser(): void
    {
        $grantedUserId = Uuid::v4();
        $requestedUserId = Uuid::v4();
        $grantedEmail = 'otheremail@example.com';
        $emailRequested = 'test@example.com';
        /** @var User $grantedUser */
        $grantedUser = UserFactory::createOne(['userId' => $grantedUserId, 'email' => $grantedEmail]);
        /** @var User $requestedUser */
        $requestedUser = UserFactory::createOne(['userId' => $requestedUserId, 'email' => $emailRequested]);
        $userTransformed = [
            'user_id' => $requestedUser->getId()->toString(),
            'first_name' => $requestedUser->getFirstName(),
            'last_name' => $requestedUser->getLastName(),
        ];

        $this->userGrantedMock->expects($this->once())
            ->method('getUser')
            ->willReturn($grantedUser);
        $this->userRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($requestedUser->getId())
            ->willReturn($requestedUser);
        $this->userTransformerMock->expects($this->once())
            ->method('transform')
            ->with($requestedUser)
            ->willReturn($userTransformed);

        $transformer = $this->createMock(UserTransformer::class);
        $transformer->method('transform')
            ->with($requestedUser)
            ->willReturn([
            'id' => $requestedUserId->toRfc4122(),
            'email' => 'test@example.com',
            'roles' => ['ROLE_USER'],
        ]);

        $query = new GetUserByIdQuery($requestedUserId);

        $result = $this->handler->__invoke($query);

        $this->assertInstanceOf(GetUserByIdQueryResult::class, $result);
        $this->assertEquals($requestedUserId->toString(), $result->getUser()['user_id']);
        $this->assertArrayNotHasKey('email', $result->getUser());
    }
}
