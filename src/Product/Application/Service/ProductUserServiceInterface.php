<?php declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Shared\Domain\ValueObject\GeoLocation;
use Symfony\Component\Uid\Uuid;

interface ProductUserServiceInterface
{
    public function getUserLocationByUserId(Uuid $userId): GeoLocation;
}
