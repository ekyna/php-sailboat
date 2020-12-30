<?php

namespace App\Service\Serializer;

use App\Entity\Position;
use DateTime;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class PositionDenormalizer
 * @package App\Service\Serializer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PositionDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            $position = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        } else {
            $position = new Position();
        }

        if (isset($data['date'])) {
            $position->setDate(new Datetime($data['longitude']));
        }

        if (isset($data['longitude'])) {
            $position->setLongitude((int)$data['longitude']);
        }

        if (isset($data['latitude'])) {
            $position->setLatitude((int)$data['latitude']);
        }

        if (isset($data['direction'])) {
            $position->setDirection((int)$data['direction']);
        }

        if (isset($data['speed'])) {
            $position->setDirection((int)$data['speed']);
        }

        return $position;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return $type === Position::class && $format === 'json';
    }
}
