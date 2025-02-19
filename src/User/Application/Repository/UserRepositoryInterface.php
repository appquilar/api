<?php

declare(strict_types=1);

namespace App\User\Application\Repository;

use App\Shared\Application\Repository\RepositoryInterface;
use App\User\Domain\Entity\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
