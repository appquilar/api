<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Request\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CategoryExists extends Constraint
{
    public function __construct(public ?string $message = null)
    {
        parent::__construct();
    }
}
