<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CustomType;

use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class DateTimeMicrosecondsType extends Type
{
    private const TYPENAME = 'datetime_microseconds';
    private const FORMAT = 'Y-m-d H:i:s.u';
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'DATETIME(6)';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        $val = \DateTime::createFromFormat(self::FORMAT, $value);

        if ( ! $val) {
            $val = date_create($value);
        }

        if ( ! $val) {
            throw new ConversionException($value . ' is not a valid ' . self::TYPENAME);
        }

        return $val;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(self::FORMAT);
        }

        throw new ConversionException($value . ' is not a valid ' . self::TYPENAME);
    }

    public function getName(): string
    {
        return self::TYPENAME;
    }
}
