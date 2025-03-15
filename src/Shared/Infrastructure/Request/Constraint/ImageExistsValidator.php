<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Request\Constraint;

use App\Shared\Infrastructure\Service\ImageServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ImageExistsValidator extends ConstraintValidator
{
    public function __construct(
        private ImageServiceInterface $imageService
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ImageExists) {
            throw new UnexpectedTypeException($constraint, ImageExists::class);
        }

        if (!$this->imageService->imageExistsById($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
