<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetCompanyUsersByCompanyIdDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "company.get_users.company_id.not_blank"),
            new Assert\Uuid(message: "company.get_users.company_id.uuid")
        ])]
        public readonly ?Uuid $companyId = null,
        public readonly int $page = 1,
        public readonly int $perPage = 10
    ) {
    }
}
