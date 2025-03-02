<?php

declare(strict_types=1);

namespace App\Shared\Application\Exception\Unauthorized;

class NotEnoughPermissionsException extends UnauthorizedException
{
    protected $message = 'Not enough permissions';
}
