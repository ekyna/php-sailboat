<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BoatController
 * @package App\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BoatController
{
    /**
     * @var BoatRepository
     */
    private $boatRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;


    /**
     * Constructor.
     *
     * @param BoatRepository         $boatRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface    $serializer
     * @param ValidatorInterface     $validator
     */
    public function __construct(
        BoatRepository $boatRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->boatRepository = $boatRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function index(): Response
    {
        $boats = $this->boatRepository->findAll();

        $data = [
            'boats' => $boats,
        ];

        $json = $this->serializer->serialize($data, 'json');

        return new Response($json, Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function post(Request $request): Response
    {
        /** @var Boat $boat */
        $boat = $this
            ->serializer
            ->deserialize($request->getContent(), Boat::class, 'json');

        $violations = $this->validator->validate($boat);
        if (0 < $violations->count()) {
            return $this->buildViolationsResponse($violations);
        }

        $this->entityManager->persist($boat);
        $this->entityManager->flush();

        $json = $this
            ->serializer
            ->serialize([
                'boat' => $boat,
            ], 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    public function get(Request $request): Response
    {
        $id = $request->attributes->getInt('boatId');

        if (null === $boat = $this->boatRepository->find($id)) {
            throw new NotFoundHttpException();
        }

        $json = $this->serializer->serialize([
            'boat' => $boat,
        ], 'json');

        return new Response($json, Response::HTTP_CREATED, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function patch(Request $request): Response
    {
        $id = $request->attributes->getInt('boatId');

        if (null === $boat = $this->boatRepository->find($id)) {
            throw new NotFoundHttpException();
        }

        $json = $this
            ->serializer
            ->deserialize($request->getContent(), 'json', Boat::class, [
                AbstractNormalizer::OBJECT_TO_POPULATE => $boat,
            ]);

        $this->entityManager->persist($boat);
        $this->entityManager->flush();

        return new Response($json, Response::HTTP_ACCEPTED, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Builds a response from the constraint violation list.
     *
     * @param ConstraintViolationListInterface $violations
     *
     * @return Response
     */
    private function buildViolationsResponse(ConstraintViolationListInterface $violations): Response
    {
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $data = [
            'errors' => $errors,
        ];

        return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
    }
}
