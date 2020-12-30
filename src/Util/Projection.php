<?php

namespace App\Util;

/**
 * Class Projection
 * @package App\Util
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class Projection
{
    private const RADIUS    = 6378137;
    private const HALF_SIZE = M_PI * self::RADIUS;


    /**
     * Transforms EPSG:4326 (degrees) to EPSG:3857 (meters).
     *
     * @param float $lon
     * @param float $lat
     *
     * @return float[]
     */
    public static function toEPSG3857(float $lon, float $lat): array
    {
        $lon = self::HALF_SIZE * $lon / 180;
        $lat = self::RADIUS * log(tan((M_PI * ($lat + 90)) / 360));

        if ($lat > self::HALF_SIZE) {
            $lat = self::HALF_SIZE;
        } elseif ($lat < -self::HALF_SIZE) {
            $lat = -self::HALF_SIZE;
        }

        return [$lon, $lat];
    }

    /**
     * Transforms EPSG:3857 (meters) to EPSG:4326 (degrees).
     *
     * @param float $lon
     * @param float $lat
     *
     * @return float[]
     */
    public static function toEPSG4326(float $lon, float $lat): array
    {
        $lon = 180 * $lon / self::HALF_SIZE;
        $lat = 360 * atan(exp($lat / self::RADIUS)) / M_PI - 90;

        return [$lon, $lat];
    }

    /** Disabled constructor */
    private function __construct()
    {
    }
}
