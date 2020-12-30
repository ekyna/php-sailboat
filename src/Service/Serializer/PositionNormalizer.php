<?php

namespace App\Service\Serializer;

use App\Entity\Position;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PositionNormalizer
 * @package App\Service\Serializer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PositionNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var Position $object */

        return [
            'id'        => $object->getId(),
            'date'      => $object->getDate()->format('Y-m-d H:i:s'),
            'longitude' => $object->getLongitude(),
            'latitude'  => $object->getLatitude(),
            'direction' => $object->getDirection(),
            'speed'     => $object->getSpeed(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Position && $format === 'json';
    }
}
