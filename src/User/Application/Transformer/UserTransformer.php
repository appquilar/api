<?php

declare(strict_types=1);

namespace App\User\Application\Transformer;

use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;
use App\User\Domain\Entity\User;

class UserTransformer implements Transformer
{
    public function __construct(
        private UserGranted $userGranted
    ) {
    }

    public function transform(User|Entity $entity): array
    {
        $data = [
            'user_id' => $entity->getId()->toString(),
            'first_name' => $entity->getFirstName(),
            'last_name' => $entity->getLastName(),
        ];

        if (
            $this->userGranted->isAdmin() ||
            $this->userGranted->getUser()->getId() === $entity->getId()
        ) {
            $data['email'] = $entity->getEmail();
        }

        if ($this->userGranted->isAdmin()) {
            $data['roles'] = $entity->getRoles();
        }

        return $data;
    }
}
