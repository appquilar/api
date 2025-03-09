<?php

declare(strict_types=1);

namespace App\Company\Application\Service;

use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

interface UserServiceInterface
{
    public function getUserById(Uuid $userId): ?User;
    public function getUserByEmail(string $email): ?User;
}
