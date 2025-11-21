<?php declare(strict_types=1);

namespace App\Tests\Factories\Product;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Product\Domain\ValueObject\Tier;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Domain\ValueObject\Money;
use Hidehalo\Nanoid\Client;
use Symfony\Component\Uid\Uuid;

trait ProductFactoryDefaultsTrait
{
    public static function class(): string
    {
        return Product::class;
    }

    protected function buildProductDefaults(): array
    {
        $shortIdClient = new Client();

        $imageIds = [];
        for ($i = 0, $n = random_int(1, 10); $i < $n; $i++) {
            $imageIds[] = Uuid::v4();
        }

        return [
            'productId'         => Uuid::v4(),
            'shortId'           => $shortIdClient->generateId(),
            'name'              => self::faker()->word(),
            'slug'              => self::faker()->slug(),
            'internalId'        => self::faker()->uuid(),
            'description'       => self::faker()->sentence(),
            'quantity'          => self::faker()->randomNumber(),
            'companyId'         => null,
            'userId'            => null,
            'categoryId'        => null,
            'imageIds'          => $imageIds,
            'publicationStatus' => PublicationStatus::default(),
            'deposit'           => $this->randomMoney(),
            'tiers'             => $this->createRandomTiers(self::faker()->randomDigitNotZero() + 1),
        ];
    }

    private function createRandomTiers(int $numberOfTiers): TierCollection
    {
        $tiers   = [];
        $dayFrom = 1;

        for ($i = 0; $i < $numberOfTiers; $i++) {
            $dayTo    = self::faker()->randomDigit() + $dayFrom;
            $tiers[]  = new Tier(
                $this->randomMoney(),
                $dayFrom,
                $dayTo
            );
            $dayFrom  = $dayTo + 1;
        }

        return new TierCollection($tiers);
    }

    private function randomMoney(): Money
    {
        return new Money(
            self::faker()->randomNumber(2) * 100,
            'EUR'
        );
    }
}
