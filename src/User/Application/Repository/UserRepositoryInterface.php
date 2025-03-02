<?php

declare(strict_types=1);

namespace App\User\Application\Repository;

use App\Shared\Application\Repository\RepositoryInterface;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

/**
 * @method User findById(Uuid $id)
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function updateUserPassword(User $user, string $newPassword): void;
}
