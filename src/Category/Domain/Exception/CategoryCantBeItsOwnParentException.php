<?php declare(strict_types=1);

namespace App\Category\Domain\Exception;

use App\Shared\Application\Exception\BadRequest\BadRequestException;

class CategoryCantBeItsOwnParentException extends BadRequestException
{
}
