<?php
// src/Service/VehicleService.php
namespace App\Service;

use App\Dto\Vehicle\CreateVehicleRequest;
use App\Dto\Vehicle\UpdateVehicleRequest;
use App\Dto\Vehicle\VehicleCreateRequest;
use App\Dto\Vehicle\VehicleUpdateRequest;
use App\Entity\Vehicle;
use App\Entity\User;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;

class VehicleService
{
    public function __construct(
        private VehicleRepository $repo,
        private EntityManagerInterface $em
    ) {}

    public function listAll(): array
    {
        return $this->repo->findAll();
    }

    public function createVehicle(User $owner, CreateVehicleRequest $dto): Vehicle
    {
        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
                ->setBrand($dto->brand)
                ->setModel($dto->model)
                ->setYearEntity($dto->year)
                ->setFipeValue($dto->fipeValue)
                ->setSalePrice($dto->salePrice)
                ->setStatus('for_sale');
        $this->em->persist($vehicle);
        $this->em->flush();
        return $vehicle;
    }

    public function updateVehicle(Vehicle $vehicle, UpdateVehicleRequest $dto): Vehicle
    {
        if ($dto->salePrice !== null) {
            $vehicle->setSalePrice($dto->salePrice);
        }
        if ($dto->status !== null) {
            $vehicle->setStatus($dto->status);
        }
        $this->em->flush();
        return $vehicle;
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        $this->em->remove($vehicle);
        $this->em->flush();
    }
}