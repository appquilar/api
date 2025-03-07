<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueOwnerId extends Constraint
{
    public ?string $message = null;
    public function __construct(string $message)
    {
        parent::__construct();

        $this->message = $message;
    }
}
