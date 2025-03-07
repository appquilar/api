<?php

declare(strict_types=1);

namespace App\Company\Application\Transformer;

use App\Company\Domain\Entity\Company;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;

class CompanyTransformer implements Transformer
{
    public function __construct(
        private UserGranted $userGranted
    ) {
    }

    public function transform(Company|Entity $entity): array
    {
        $data = [
            'company_id' => $entity->getId(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'owner_id' => $entity->getOwnerId()->toString(),
            'address' => $entity->getAddress(),
            'postal_code' => $entity->getPostalCode(),
            'city' => $entity->getCity()
        ];

        if ($this->userGranted->isAdmin() || $this->userGranted->worksAtThisCompany($entity->getId())) {
            $data['contact_email'] = $entity->getContactEmail();
            $data['phone_number'] = $entity->getPhoneNumber()?->toArray();
            $data['fiscal_identifier'] = $entity->getFiscalIdentifier();
        }

        return $data;
    }
}
