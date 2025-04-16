<?php
// src/Service/VehicleService.php
namespace App\Service;

use App\Dto\Vehicle\CreateVehicleRequest;
use App\Dto\Vehicle\UpdateVehicleRequest;
use App\Entity\Vehicle;
use App\Entity\User;
use App\Enum\VehicleType;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ModelRepository;
use App\Repository\YearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CategoryRepository $categoryRepo,
        private BrandRepository $brandRepo,
        private ModelRepository $modelRepo,
        private YearRepository $yearRepo,
        private FipeApiService $fipeApi
    ) {}

    /**
     * Cria um veículo atrelado a um usuário, usando dados locais e API FIPE para fipeValue.
     */
    public function createVehicle(string $userEmail, CreateVehicleRequest $dto): Vehicle
    {
        // Categoria
        $category = $this->categoryRepo->findOneBy(['name' => $dto->category]);
        if (!$category) {
            throw new NotFoundHttpException("Categoria '{$dto->category}' não encontrada.");
        }

        // Marca
        $brand = $this->brandRepo->findOneBy(['fipeCode' => $dto->brand]);
        if (!$brand) {
            throw new NotFoundHttpException("Marca '{$dto->brand}' não encontrada.");
        }

        // Modelo
        $model = $this->modelRepo->findOneBy(['fipeCode' => $dto->model]);
        if (!$model) {
            throw new NotFoundHttpException("Modelo '{$dto->model}' não encontrado.");
        }

        // Ano
        $years = $this->yearRepo->findBy(['model' => $model]);
        $yearEntity = null;
        foreach ($years as $y) {
            if ((int)substr($y->getFipeCode(), 0, 4) === $dto->year) {
                $yearEntity = $y;
                break;
            }
        }
        if (!$yearEntity) {
            throw new NotFoundHttpException("Ano '{$dto->year}' não encontrado para o modelo.");
        }

        // Busca valor FIPE via API
        $tipoEnum = VehicleType::from($dto->category);
        $details = $this->fipeApi->getVehicleDetails(
            $tipoEnum,
            $brand->getFipeCode(),
            $model->getFipeCode(),
            $yearEntity->getFipeCode()
        );
        $fipeValue = preg_replace('/[^0-9.,]/', '', $details['Valor']);

        // Monta entidade
        $vehicle = new Vehicle();
        $vehicle->setCategory($category)
                ->setBrand($brand)
                ->setModel($model)
                ->setYearEntity($yearEntity)
                ->setFipeValue($fipeValue)
                ->setSalePrice(null)
                ->setStatus('for_sale')
                ->setUserByEmail($userEmail); // implementado no controller

        $this->em->persist($vehicle);
        $this->em->flush();
        return $vehicle;
    }

    /**
     * Atualiza status e salePrice de um veículo, e opcionalmente reposiciona dados FIPE.
     */
    public function updateVehicle(Vehicle $vehicle, UpdateVehicleRequest $dto): Vehicle
    {
        if ($dto->status !== null) {
            $vehicle->setStatus($dto->status);
        }
        // Opcional: recálculo de fipeValue se ano/modelo mudar
        // if ($dto->year || $dto->model || $dto->brand) { ... }

        $this->em->flush();
        return $vehicle;
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        $this->em->remove($vehicle);
        $this->em->flush();
    }

    /**
     * Transfere propriedade via email
     */
    public function transferOwnership(Vehicle $vehicle, string $newOwnerEmail): Vehicle
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $newOwnerEmail]);
        if (!$user) {
            throw new NotFoundHttpException("Usuário '{$newOwnerEmail}' não encontrado.");
        }
        $vehicle->setUser($user);
        $this->em->flush();
        return $vehicle;
    }
}