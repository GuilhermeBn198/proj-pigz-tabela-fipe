<?php
// src/Service/VehicleService.php
namespace App\Service;

use App\Dto\Vehicle\CreateVehicleRequest;
use App\Dto\Vehicle\UpdateVehicleRequest;
use App\Dto\Vehicle\TransferVehicleRequest;
use App\Entity\Vehicle;
use App\Entity\User;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ModelRepository;
use App\Repository\YearRepository;
use App\Repository\UserRepository;
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
        private UserRepository $userRepo,
        private FipeApiService $fipeApi
    ) {}

    /**
     * Retorna todos os veículos cadastrados.
     *
     * @return Vehicle[]
     */
    public function listAll(): array
    {
        return $this->em->getRepository(Vehicle::class)->findAll();
    }

    /**
     * Cria um veículo atrelado a um usuário, usando dados locais e a API FIPE para o valor.
     */
    public function createVehicle(User $owner, CreateVehicleRequest $dto): Vehicle
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

        // Ano: busca pelo código FIPE completo (ex: "2014-1")
        $yearEntity = $this->yearRepo->findOneBy([
            'model'    => $model,
            'fipeCode' => $dto->yearCode
        ]);
        if (!$yearEntity) {
            throw new NotFoundHttpException("Ano '{$dto->yearCode}' não encontrado para o modelo.");
        }

        // Busca valor FIPE via API
        $details = $this->fipeApi->getVehicleDetails(
            $brand->getType(),
            $brand->getFipeCode(),
            $model->getFipeCode(),
            $yearEntity->getFipeCode()
        );
        $fipeValue = preg_replace('/[^0-9,]/', '', $details['Valor']);
        $fipeValue = str_replace(',', '.', $fipeValue);
        $fipeValue = (float) $fipeValue;
        
        // Monta entidade Vehicle
        $vehicle = new Vehicle();
        $vehicle->setUser($owner)
            ->setCategory($category)
            ->setBrand($brand)
            ->setModel($model)
            ->setYearEntity($yearEntity)
            ->setFipeValue($fipeValue)
            ->setSalePrice((string)$dto->salePrice)
            ->setStatus('for_sale');
        

        $this->em->persist($vehicle);
        $this->em->flush();

        return $vehicle;
    }

    /**
     * Atualiza o salePrice e o status de um veículo.
     */
    public function updateVehicle(Vehicle $vehicle, UpdateVehicleRequest $dto): Vehicle
    {
        if ($dto->salePrice !== null) {
            $vehicle->setSalePrice((string)$dto->salePrice);
        }
        if ($dto->status !== null) {
            $vehicle->setStatus($dto->status);
        }
        $this->em->flush();

        return $vehicle;
    }

    /**
     * Remove um veículo.
     */
    public function deleteVehicle(Vehicle $vehicle): void
    {
        $this->em->remove($vehicle);
        $this->em->flush();
    }

    /**
     * Efetua a transferência de propriedade do veículo.
     *
     * Adaptação do fluxo de transferência:
     * - Busca o novo proprietário via email.
     * - Atualiza o veículo com o novo dono.
     * - Define o status como 'sold'.
     * - Registra o instante da venda em 'soldAt'.
     *
     * Essa implementação considera que as verificações de permissão já foram
     * realizadas via Voter no controller.
     */
    public function transferOwnership(Vehicle $vehicle, TransferVehicleRequest $dto): Vehicle
    {
        $user = $this->userRepo->findOneBy(['email' => $dto->newOwnerEmail]);
        if (!$user) {
            throw new NotFoundHttpException("Usuário '{$dto->newOwnerEmail}' não encontrado.");
        }

        // Atualiza o veículo: novo proprietário, status vendido e registra o momento da venda.
        $vehicle->setUser($user);
        $vehicle->setStatus('sold');
        $vehicle->setSoldAt(new \DateTime());

        $this->em->flush();

        return $vehicle;
    }
}
