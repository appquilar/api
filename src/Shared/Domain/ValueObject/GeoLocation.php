<?php declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Exception\InvalidGeoLocationException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class GeoLocation
{
    private const int EARTH_RADIUS_METERS = 6371000;

    public function __construct(
        #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
        private float $latitude,
        #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
        private float $longitude
    ) {
        if ($this->latitude < -90 || $this->latitude > 90) {
            throw new InvalidGeoLocationException('geolocation.latitude.invalid_value');
        }
        if ($this->longitude < -180 || $this->longitude > 180) {
            throw new InvalidGeoLocationException('geolocation.longitude.invalid_value');
        }
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    /**
     * @return array<int, array{lat: float, lng: float}>
     */
    public function generateCircle(float $radiusMeters = 2000, int $points = 36): array
    {
        [$centerLat, $centerLng] = $this->offsetLocation();

        $centerLatRad = deg2rad($centerLat);
        $centerLngRad = deg2rad($centerLng);

        $coords           = [];
        $angleStep        = 360 / $points;
        $angularDistance  = $radiusMeters / self::EARTH_RADIUS_METERS;

        for ($i = 0; $i < $points; $i++) {
            $bearingRad = deg2rad($i * $angleStep);

            [$pointLatRad, $pointLngRad] = $this->movePointOnSphere(
                $centerLatRad,
                $centerLngRad,
                $angularDistance,
                $bearingRad
            );

            $coords[] = [
                'latitude' => rad2deg($pointLatRad),
                'longitude' => rad2deg($pointLngRad),
            ];
        }

        return $coords;
    }

    /**
     * @return array{0: float, 1: float} [lat, lng] en grados
     */
    private function offsetLocation(float $minMeters = 250, float $maxMeters = 750): array
    {
        $distance = $minMeters + (mt_rand() / mt_getrandmax()) * ($maxMeters - $minMeters);
        $bearing  = (mt_rand() / mt_getrandmax()) * 360;

        $latRad       = deg2rad($this->latitude);
        $lngRad       = deg2rad($this->longitude);
        $bearingRad   = deg2rad($bearing);
        $angularDist  = $distance / self::EARTH_RADIUS_METERS;

        [$newLatRad, $newLngRad] = $this->movePointOnSphere(
            $latRad,
            $lngRad,
            $angularDist,
            $bearingRad
        );

        return [rad2deg($newLatRad), rad2deg($newLngRad)];
    }

    /**
     * @return array{0: float, 1: float} [latRad, lngRad]
     */
    private function movePointOnSphere(
        float $latRad,
        float $lngRad,
        float $angularDistance,
        float $bearingRad
    ): array {
        $sinLat       = sin($latRad);
        $cosLat       = cos($latRad);
        $sinAngDist   = sin($angularDistance);
        $cosAngDist   = cos($angularDistance);
        $sinBearing   = sin($bearingRad);
        $cosBearing   = cos($bearingRad);

        $newLatRad = asin(
            $sinLat * $cosAngDist +
            $cosLat * $sinAngDist * $cosBearing
        );

        $newLngRad = $lngRad + atan2(
                $sinBearing * $sinAngDist * $cosLat,
                $cosAngDist - $sinLat * sin($newLatRad)
            );

        return [$newLatRad, $newLngRad];
    }

    public function getDistanceInMeters(GeoLocation $destination): float
    {
        $earthRadius = 6371000;

        $lat1 = deg2rad($this->latitude);
        $lon1 = deg2rad($this->longitude);
        $lat2 = deg2rad($destination->getLatitude());
        $lon2 = deg2rad($destination->getLongitude());

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) ** 2 +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
