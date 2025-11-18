<?php declare(strict_types=1);

namespace App\Product\Infrastructure\CustomType;

use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\TierCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class TierCollectionType extends Type
{
    public const NAME = 'tier_collection';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @throws \JsonException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof TierCollection) {
            throw new \InvalidArgumentException(
                sprintf('Expected %s, got %s', TierCollection::class, get_debug_type($value))
            );
        }

        return json_encode($value->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * @throws InvalidPriceConstructionException
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): TierCollection
    {
        if ($value === null || $value === '') {
            return new TierCollection([]);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            return TierCollection::fromArray($decoded);
        }

        // For hydration edge cases (when DBAL may already decode json)
        if (is_array($value)) {
            return TierCollection::fromArray($value);
        }

        throw new \InvalidArgumentException(
            sprintf('Cannot convert value of type %s to TierCollection', get_debug_type($value))
        );
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'JSON';
    }
}