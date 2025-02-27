<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\DoctrineRepository;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Domain\Entity\User;

class UserRepository extends DoctrineRepository implements UserRepositoryInterface
{
    public function getClass(): string
    {
        return User::class;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function updateUserPassword(User $user, string $newPassword): void
    {
        $user->setPassword($newPassword);
        $user->setWordPressPassword(null); // 🔹 Remove old hash after migration
        $this->save($user);
    }
}
