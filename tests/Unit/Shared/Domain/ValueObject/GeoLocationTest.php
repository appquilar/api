<?php declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\GeoLocation;
use App\Shared\Exception\InvalidGeoLocationException;
use App\Tests\Unit\UnitTestCase;

class GeoLocationTest extends UnitTestCase
{
    public function testCreateValidGeoLocation(): void
    {
        $geo = new GeoLocation(41.5381, 2.4445);

        self::assertSame(41.5381, $geo->getLatitude());
        self::assertSame(2.4445, $geo->getLongitude());
    }

    public function testLatitudeOutOfRangeThrowsException(): void
    {
        $this->expectException(InvalidGeoLocationException::class);

        new GeoLocation(91.0, 2.0);
    }

    public function testLongitudeOutOfRangeThrowsException(): void
    {
        $this->expectException(InvalidGeoLocationException::class);

        new GeoLocation(41.0, 181.0);
    }

    public function testToArrayReturnsLatAndLng(): void
    {
        $geo = new GeoLocation(41.5381, 2.4445);

        $array = $geo->toArray();

        self::assertArrayHasKey('latitude', $array);
        self::assertArrayHasKey('longitude', $array);
        self::assertSame(41.5381, $array['latitude']);
        self::assertSame(2.4445, $array['longitude']);
    }

    public function testGenerateCircleReturnsExpectedNumberOfPoints(): void
    {
        $geo = new GeoLocation(41.5381, 2.4445);

        $points = $geo->generateCircle(2000, 36);

        self::assertCount(36, $points);

        foreach ($points as $point) {
            self::assertIsArray($point);
            self::assertArrayHasKey('latitude', $point);
            self::assertArrayHasKey('longitude', $point);
            self::assertGreaterThanOrEqual(-90.0, $point['latitude']);
            self::assertLessThanOrEqual(90.0, $point['latitude']);
            self::assertGreaterThanOrEqual(-180.0, $point['longitude']);
            self::assertLessThanOrEqual(180.0, $point['longitude']);
        }

        // Al menos dos puntos deben ser distintos (no todos iguales)
        $first = $points[0];
        $differentFound = false;
        foreach ($points as $point) {
            if ($point['latitude'] !== $first['latitude'] || $point['longitude'] !== $first['longitude']) {
                $differentFound = true;
                break;
            }
        }

        self::assertTrue($differentFound, 'Circle points should not all be identical.');
    }
}
