<?php

declare(strict_types=1);

namespace App\Shared\Application\Exception\NotFound;

use Symfony\Component\Uid\Uuid;

class EntityNotFoundException extends NotFoundException
{
    protected $message = 'Entity with id %s not found';

    public function __construct(Uuid $id)
    {
        parent::__construct(
            sprintf($this->message, $id->toString())
        );
    }
}
