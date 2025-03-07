<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Service;

use App\Company\Application\Service\UserServiceInterface;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function getUserById(Uuid $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }
}
