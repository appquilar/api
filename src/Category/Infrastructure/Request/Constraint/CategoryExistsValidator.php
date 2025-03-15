<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\Request\Constraint;

use App\Category\Application\Repository\CategoryRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CategoryExistsValidator extends ConstraintValidator
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CategoryExists) {
            throw new UnexpectedTypeException($constraint, CategoryExists::class);
        }

        if ($this->categoryRepository->findById($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
