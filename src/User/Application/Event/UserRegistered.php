<?php

declare(strict_types=1);

namespace App\User\Application\Event;

use Symfony\Component\Uid\Uuid;

class UserRegistered
{
    public function __construct(
        private Uuid $userId,
        private string $email
    ) {
    }
}
