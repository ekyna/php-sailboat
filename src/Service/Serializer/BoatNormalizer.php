<?php

namespace App\Service\Serializer;

use App\Entity\Boat;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Class BoatNormalizer
 * @package App\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BoatNormalizer implements SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var Boat $object */

        return [
            'id'        => $object->getId(),
            'name'      => $object->getName(),
            'position'  => $this->serializer->normalize($object->getPosition(), $format, $context),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Boat && $format === 'json';
    }
}
