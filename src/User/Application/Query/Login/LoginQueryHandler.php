<?php

declare(strict_types=1);

namespace App\User\Application\Query\Login;

use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Application\Query\QueryInterface;
use App\User\Application\Dto\TokenPayload;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use App\User\Domain\Entity\User;
use Hautelook\Phpass\PasswordHash;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(handles: LoginQuery::class)]
class LoginQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private AuthTokenServiceInterface $authTokenService,
    ) {
    }

    /**
     * @throws UnauthorizedException
     */
    public function __invoke(LoginQuery|QueryInterface $query): LoginQueryResult
    {
        $user = $this->userRepository->findByEmail($query->getEmail());
        if (!$user) {
            throw new UnauthorizedException('User not found');
        }

        if ($user->getWordPressPassword()) {
            $this->validateWordpressPassword($query, $user);
        } else {
            if (!$this->passwordHasher->isPasswordValid($user, $query->getPassword())) {
                throw new UnauthorizedException('Invalid password');
            }
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
            $newPasswordHash = $this->passwordHasher->hashPassword($user, $query->getPassword());
            $this->userRepository->updateUserPassword($user, $newPasswordHash);
        } else {
            throw new UnauthorizedException();
        }
    }
}
