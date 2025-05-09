<?php

namespace App\Controller;

use App\Dto\Vehicle\CreateVehicleRequest;
use App\Dto\Vehicle\UpdateVehicleRequest;
use App\Dto\Vehicle\TransferVehicleRequest;
use App\Entity\Vehicle;
use App\Service\VehicleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VehicleController extends AbstractController
{
    public function __construct(private VehicleService $svc) {}

    #[Route('/api/vehicles', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listAll(): JsonResponse
    {
        $vehicles = $this->svc->listAll();
        return $this->json($vehicles, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/sold', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listAllSold(): JsonResponse
    {
        $vehicles = $this->svc->listAllSold();
        return $this->json($vehicles, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/for-sale', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function listAllForSale(): JsonResponse
    {
        $vehicles = $this->svc->listAllForSale();
        return $this->json($vehicles, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}/request', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function requestPurchase(Vehicle $vehicle): JsonResponse
    {
        $buyer = $this->getUser();
        $vehicle = $this->svc->requestPurchase($vehicle, $buyer);

        return $this->json($vehicle, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}/accept-request', methods: ['POST'])]
    #[IsGranted('VEHICLE_EDIT', subject: 'vehicle')]
    public function acceptRequest(Vehicle $vehicle): JsonResponse
    {
        $vehicle = $this->svc->acceptPurchaseRequest($vehicle);
        return $this->json($vehicle, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}/reject-request', methods: ['POST'])]
    #[IsGranted('VEHICLE_EDIT', subject: 'vehicle')]
    public function rejectRequest(Vehicle $vehicle): JsonResponse
    {
        $vehicle = $this->svc->rejectPurchaseRequest($vehicle);
        return $this->json($vehicle, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }


    #[Route('/api/vehicles', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(
        #[MapRequestPayload] CreateVehicleRequest $dto
    ): JsonResponse {
        $owner = $this->getUser();
        $vehicle = $this->svc->createVehicle($owner, $dto);
        return $this->json($vehicle, JsonResponse::HTTP_CREATED, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}', methods: ['GET'])]
    #[IsGranted('VEHICLE_EDIT', subject: 'vehicle')]
    public function show(Vehicle $vehicle): JsonResponse
    {
        return $this->json($vehicle, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}', methods: ['PATCH'])]
    #[IsGranted('VEHICLE_EDIT', subject: 'vehicle')]
    public function update(
        Vehicle $vehicle,
        #[MapRequestPayload] UpdateVehicleRequest $dto
    ): JsonResponse {
        $vehicle = $this->svc->updateVehicle($vehicle, $dto);
        return $this->json($vehicle, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}', methods: ['DELETE'])]
    #[IsGranted('VEHICLE_DELETE', subject: 'vehicle')]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        $this->svc->deleteVehicle($vehicle);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    // #[Route('/api/vehicles/{id}/transfer', methods: ['POST'])]
    // #[IsGranted('VEHICLE_TRANSFER', subject: 'vehicle')]
    // public function transfer(
    //     Vehicle $vehicle,
    //     #[MapRequestPayload] TransferVehicleRequest $dto
    // ): JsonResponse {
    //     $vehicle = $this->svc->transferOwnership($vehicle, $dto);
    //     return $this->json($vehicle, JsonResponse::HTTP_OK, [], ['groups' => 'vehicle:read']);
    // }
}
