<?php

namespace App\Service;

use App\Entity\Boat;
use App\Util\Projection;

/**
 * Class BoatMover
 * @package App\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BoatMover
{
    /**
     * @var WindProvider
     */
    private $windProvider;


    /**
     * Moves the boat.
     *
     * @param Boat $boat
     */
    public function move(Boat $boat): void
    {
        // TODO

        [$x, $y] = Projection::toEPSG3857($boat->getLongitude(), $boat->getLatitude());


    }
}
