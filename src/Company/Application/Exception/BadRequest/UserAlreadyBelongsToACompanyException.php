<?php

declare(strict_types=1);

namespace App\Company\Application\Exception\BadRequest;

use App\Shared\Application\Exception\BadRequest\BadRequestException;

class UserAlreadyBelongsToACompanyException extends BadRequestException
{
    protected $message = 'User already belongs to a company';
}
