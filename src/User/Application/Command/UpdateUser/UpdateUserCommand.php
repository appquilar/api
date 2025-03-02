<?php

declare(strict_types=1);

namespace App\User\Application\Command\UpdateUser;

use App\Shared\Application\Command\Command;
use App\Shared\Infrastructure\Security\UserRole;
use Symfony\Component\Uid\Uuid;

class UpdateUserCommand implements Command
{
    /**
     * @param UserRole[]|null $roles
     */
    public function __construct(
        private Uuid $userId,
        private string $firstName,
        private string $lastName,
        private string $email,
        private array $roles = []
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }
}
