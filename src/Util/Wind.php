<?php

namespace App\Util;

/**
 * Class Wind
 * @package App\Util
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @see     https://confluence.ecmwf.int/pages/viewpage.action?pageId=133262398
 */
final class Wind
{
    /**
     * Converts wind direction and speed into UV components.
     *
     * @param float $direction
     * @param float $speed
     *
     * @return array
     */
    public static function uv(float $direction, float $speed): array
    {
        $rad = $direction * M_PI / 180;
        $u = -$speed * sin($rad);
        $v = -$speed * cos($rad);

        return [$u, $v];
    }

    /**
     * Converts UV components into direction and speed.
     *
     * @param float $u
     * @param float $v
     *
     * @return array
     */
    public function dirSpeed(float $u, float $v): array
    {
        $direction = 180 + 180 / M_PI * atan2($v, $u);
        $speed = sqrt($u * $u + $v * $v);

        return [$direction, $speed];
    }

    /** Disabled constructor */
    private function __construct()
    {
    }
}
