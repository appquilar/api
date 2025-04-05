<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Request\Constraint;

use App\Shared\Infrastructure\Service\CategoryServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CategoryExistsValidator extends ConstraintValidator
{
    public function __construct(
        private CategoryServiceInterface $categoryServicez,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof CategoryExists) {
            throw new UnexpectedTypeException($constraint, ImageExists::class);
        }

        if (!$this->categoryServicez->categoryExistsById($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
