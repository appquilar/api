<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EmailIsUnique extends Constraint
{
    public ?string $message = null;
    public function __construct(string $message)
    {
        parent::__construct();

        $this->message = $message;
    }
}
