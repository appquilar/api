<?php

declare(strict_types=1);

namespace App\Shared\Application\Context;

use App\Shared\Infrastructure\Security\UserRole;
use App\User\Domain\Entity\User;
use Zeelo\API\Domain\Common\Constants;

class UserGranted
{
    private static ?UserGranted $me = null;
    private ?User $user;
    private ?string $token;

    private function __construct() {
        $this->clear();
    }

    public static function me(): UserGranted
    {
        if (null === static::$me) {
            static::$me = new self();
        }

        return static::$me;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function clear(): void
    {
        static::$me = null;
        $this->user = null;
    }

    public function isAdmin(): bool
    {
        return in_array(UserRole::ADMIN, $this->user->getRoles());
    }
}
