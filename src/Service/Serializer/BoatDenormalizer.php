<?php

namespace App\Service\Serializer;

use App\Entity\Boat;
use App\Entity\Position;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Class BoatDenormalizer
 * @package App\Service\Serializer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BoatDenormalizer implements SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            $boat = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        } else {
            $boat = new Boat();
        }

        if (isset($data['position'])) {
            $position = $this
                ->serializer
                ->denormalize($data['position'], Position::class, $format, array_replace($context, [
                    AbstractNormalizer::OBJECT_TO_POPULATE => new Position(),
                ]));

            $boat->setPosition($position);
        }

        return $boat;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return $type === Boat::class && $format === 'json';
    }
}
