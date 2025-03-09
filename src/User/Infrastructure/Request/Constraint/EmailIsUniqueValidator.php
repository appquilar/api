<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Request\Constraint;

use App\User\Application\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailIsUniqueValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailIsUnique) {
            throw new UnexpectedTypeException($constraint, EmailIsUnique::class);
        }

        $userWithEmail = $this->userRepository->findByEmail(strtolower($value));

        if ($userWithEmail !== null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
