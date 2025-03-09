<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Request;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyUserAcceptInvitationDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: "company.accept_invitation.company_id.not_blank")]
        #[Assert\Uuid(message: "company.accept_invitation.company_id.uuid")]
        public ?Uuid $companyId = null,

        #[Assert\NotBlank(message: "company.accept_invitation.token.not_blank")]
        public ?string $token = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "company.accept_invitation.email.not_blank"),
                new Assert\Email(message: "company.accept_invitation.email.email")
            ]),
            new Assert\IsNull()
        ])]
        public ?string $email = null,

        #[Assert\AtLeastOneOf([
            new Assert\Sequentially([
                new Assert\NotBlank(message: "company.accept_invitation.new_password.not_blank"),
                new Assert\Length(min: 6, minMessage: "company.accept_invitation.new_password.length.min")
            ]),
            new Assert\IsNull()
        ])]
        public ?string $password = null,
    ) {
    }
}
