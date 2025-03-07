<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request\Constraint;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueOwnerIdValidator extends ConstraintValidator
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueOwnerId) {
            throw new UnexpectedTypeException($constraint, UniqueOwnerId::class);
        }

        $company = $this->companyRepository->findOneByOwnerId($value);

        if ($company !== null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
