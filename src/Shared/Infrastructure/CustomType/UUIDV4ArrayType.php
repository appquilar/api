<?php declare(strict_types=1);

namespace App\Shared\Infrastructure\CustomType;

use App\Product\Domain\Exception\InvalidPriceConstructionException;
use App\Product\Domain\ValueObject\TierCollection;
use App\Shared\Infrastructure\Service\UuidV4ArrayTransformer;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Uid\Uuid;

class UUIDV4ArrayType extends Type
{
    public const NAME = 'uuidv4_array';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param Uuid[] $value
     * @param AbstractPlatform $platform
     * @return string
     * @throws \JsonException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if ($value === null) {
            return json_encode([], JSON_THROW_ON_ERROR);
        }

        $arrayOfUuids = UuidV4ArrayTransformer::toArray($value);

        return json_encode($arrayOfUuids, JSON_THROW_ON_ERROR);
    }

    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return Uuid[]
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        if ($value === null) {
            return [];
        }

        return UuidV4ArrayTransformer::fromArray(json_decode($value));
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'JSON';
    }
}