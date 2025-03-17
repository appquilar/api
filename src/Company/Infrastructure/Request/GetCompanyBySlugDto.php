<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetCompanyBySlugDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "company.get_by_slug.slug.not_blank"),
        ])]
        public ?string $slug = null
    ) {
    }
}
