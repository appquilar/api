<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class RemoveCompanyUserDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "company.update_user_role.company_id.not_blank")]
        #[Assert\Uuid(message: "company.update_user_role.company_id.uuid")]
        public readonly ?Uuid $companyId = null,
        #[Assert\NotBlank(message: "company.update_user_role.user_id.not_blank")]
        #[Assert\Uuid(message: "company.update_user_role.user_id.uuid")]
        public readonly ?Uuid $userId = null,
    ) {
    }
}
