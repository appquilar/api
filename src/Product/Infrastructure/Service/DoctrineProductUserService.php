<?php declare(strict_types=1);

namespace App\Product\Infrastructure\Service;

use App\Product\Application\Service\ProductUserServiceInterface;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\User\Application\Repository\UserRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class DoctrineProductUserService implements ProductUserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function getUserLocationByUserId(Uuid $userId): GeoLocation
    {
        $user = $this->userRepository->findById($userId);

        return $user->getGeoLocation();
    }
}
