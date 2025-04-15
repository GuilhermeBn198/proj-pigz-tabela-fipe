<?php
// src/Controller/VehicleController.php
namespace App\Controller;

use App\Dto\Vehicle\CreateVehicleRequest;
use App\Dto\Vehicle\UpdateVehicleRequest;
use App\Dto\Vehicle\VehicleUpdateRequest;
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

    #[Route('/api/vehicles', methods:['GET'])]
    public function listAll(): JsonResponse
    {
        $vehicles = $this->svc->listAll();
        return $this->json($vehicles, 200, [], ['groups'=>'vehicle:read']);
    }

    #[Route('/api/vehicles', methods:['POST'])]
    public function create(#[MapRequestPayload] CreateVehicleRequest $dto): JsonResponse
    {
        $vehicle = $this->svc->createVehicle($this->getUser(), $dto);
        return $this->json($vehicle, 201, [], ['groups'=>'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}', methods:['GET'])]
    public function getOne(Vehicle $vehicle): JsonResponse
    {
        return $this->json($vehicle, 200, [], ['groups'=>'vehicle:read']);
    }

    #[Route('/api/vehicles/{id}', methods:['PATCH'])]
    #[IsGranted('VEHICLE_EDIT', subject: 'vehicle')]
    public function update(Vehicle $vehicle, #[MapRequestPayload] UpdateVehicleRequest $dto): JsonResponse
    {
        $this->svc->updateVehicle($vehicle, $dto);
        return $this->json(['message'=>'Atualizado com sucesso']);
    }

    #[Route('/api/vehicles/{id}', methods:['DELETE'])]
    #[IsGranted('VEHICLE_DELETE', subject: 'vehicle')]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        $this->svc->deleteVehicle($vehicle);
        return $this->json(null, 204);
    }
}
