<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class AddUserToCompanyDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "company.add_user.company_id.not_blank")]
        #[Assert\Uuid(message: "company.add_user.company_id.uuid")]
        public ?Uuid $companyId = null,

        #[Assert\NotBlank(message: "company.add_user.company_id.not_blank")]
        #[Assert\Email(message: "company.add_user.email.email")]
        public ?string $email = null,

        public ?CompanyUserRole $role = null
    ) {
    }
}
