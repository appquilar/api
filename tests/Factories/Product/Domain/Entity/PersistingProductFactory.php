<?php

namespace App\Tests\Factories\Product\Domain\Entity;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\PublicationStatus;
use App\Product\Domain\ValueObject\Tier;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Domain\ValueObject\Money;
use Hidehalo\Nanoid\Client;
use Random\RandomException;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

class PersistingProductFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Product::class;
    }

    /**
     * @throws InvalidPriceConstructionException
     * @throws RandomException
     */
    protected function defaults(): array|callable
    {
        $shortIdClient = new Client();
        $imageIds = [];
        for ($i = 0, $n = random_int(1, 10); $i < $n; $i++) {
            $imageIds[] = Uuid::v4();
        }

        return [
            'productId' => Uuid::v4(),
            'shortId' => $shortIdClient->generateId(),
            'name' => self::faker()->word(),
            'slug' => self::faker()->slug(),
            'internalId' => self::faker()->uuid(),
            'description' => self::faker()->sentence(),
            'quantity' => self::faker()->randomNumber(),
            'companyId' => null,
            'userId' => null,
            'categoryId' => null,
            'imageIds' => $imageIds,
            'publicationStatus' => PublicationStatus::default(),
            'deposit' => $this->randomMoney(),
            'tiers' => $this->createRandomTiers( self::faker()->randomDigitNotZero() + 1),
        ];
    }

    /**
     * @throws InvalidPriceConstructionException
     */
    private function createRandomTiers(int $numberOfTiers): TierCollection
    {
        $tiers = [];
        $dayFrom = 1;

        for($i = 0; $i < $numberOfTiers; $i++) {
            $dayTo = self::faker()->randomDigit() + $dayFrom;
            $tiers[] = new Tier(
                $this->randomMoney(),
                $dayFrom,
                $dayTo
            );
            $dayFrom = $dayTo + 1;
        }

        return new TierCollection($tiers);
    }

    private function randomMoney(): Money
    {
        return new Money(
            self::faker()->randomFloat(1, 0, 100) * 100,
            'EUR'
        );
    }

    protected function initialize(): static
    {
        return $this->with($this->defaults());
    }
}