<?php

declare(strict_types=1);

namespace App\Site\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetSiteByIdDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: "site.get_by_id.site_id.not_blank"),
            new Assert\Uuid(message: "site.get_by_id.site_id.uuid"),
        ])]
        public ?Uuid $siteId = null,
    ) {
    }
}
