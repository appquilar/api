<?php

declare(strict_types=1);

namespace App\User\Application\Command\Logout;

use App\Shared\Application\Command\CommandInterface;

class LogoutCommand implements CommandInterface
{
    // Empty command because we're using the logged in user
}
