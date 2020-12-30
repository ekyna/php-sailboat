<?php

namespace App\Controller;

use App\Repository\BoatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class LoginController
 * @package App\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class LoginController
{
    /**
     * @var BoatRepository
     */
    private $boatRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;


    /**
     * Constructor.
     *
     * @param BoatRepository      $boatRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(BoatRepository $boatRepository, SerializerInterface $serializer)
    {
        $this->boatRepository = $boatRepository;
        $this->serializer = $serializer;
    }

    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || empty($data['name'])) {
            return new JsonResponse([
                'error' => 'You must provide a name'
            ], Response::HTTP_BAD_REQUEST);
        }

        $boat = $this->boatRepository->findOneBy(['name' => $data['name']]);

        if (null === $boat) {
            return new JsonResponse([
                'error' => 'Unknown boat name'
            ], Response::HTTP_BAD_REQUEST);
        }

        // TODO Check password

        $data = $this->serializer->serialize($boat, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
