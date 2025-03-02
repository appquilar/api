<?php

declare(strict_types=1);

namespace App\User\Application\Query\Login;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\Query;
use App\User\Application\Dto\TokenPayload;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Hautelook\Phpass\PasswordHash;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: LoginQuery::class)]
class LoginQueryHandler implements QueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasher $passwordHasher,
        private AuthTokenServiceInterface $authTokenService,
    ) {
    }

    /**
     * @throws UnauthorizedException
     */
    public function __invoke(LoginQuery|Query $query): LoginQueryResult
    {
        $user = $this->userRepository->findByEmail($query->getEmail());
        if (!$user) {
            throw new UnauthorizedException('User not found');
        }

        if ($user->getWordPressPassword()) {
            $this->validateWordpressPassword($query, $user);

            return new LoginQueryResult(
                $this->authTokenService->encode(
                    new TokenPayload($user->getId(), $user->getEmail())
                )
            );
        }

        if (!$this->passwordHasher->verifyPassword($query->getPassword(), $user->getPassword())) {
            throw new UnauthorizedException('Invalid password');
        }

        return new LoginQueryResult(
            $this->authTokenService->encode(
                new TokenPayload($user->getId(), $user->getEmail())
            )
        );
    }

    /**
     * @throws UnauthorizedException
     */
    public function validateWordpressPassword(LoginQuery $query, User $user): void
    {
        $wpHasher = new PasswordHash(8, true);
        if ($wpHasher->CheckPassword($query->getPassword(), $user->getWordPressPassword())) {
            $this->userRepository->updateUserPassword(
                $user,
                $this->passwordHasher->hashPassword($query->getPassword())
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}
