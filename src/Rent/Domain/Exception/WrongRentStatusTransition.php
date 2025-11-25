<?php declare(strict_types=1);

namespace App\Rent\Domain\Exception;

use App\Shared\Application\Exception\BadRequest\BadRequestException;

class WrongRentStatusTransition extends BadRequestException
{

}
