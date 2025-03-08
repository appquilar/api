<?php

declare(strict_types=1);

namespace App\Company\Application\Transformer;

use App\Company\Domain\Entity\CompanyUser;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;

class CompanyUserTransformer implements Transformer
{
    public function __construct(
        private UserGranted $userGranted,
    ) {
    }

    public function transform(CompanyUser|Entity $entity): array
    {
        if (
            !$this->userGranted->worksAtThisCompany($entity->getCompanyId()) &&
            !$this->userGranted->isAdmin()
        ) {
            throw new UnauthorizedException();
        }

        return [
            'company_id' => $entity->getCompanyId()->toString(),
            'user_id' => $entity->getUserId()?->toString(),
            'role' => $entity->getRole()->value,
            'status' => $entity->getStatus()->getStatus($entity->getInvitationExpiresAt())->value
        ];
    }
}
