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

    public function listAllForSale(): array
    {
        return $this->em->getRepository(Vehicle::class)->findBy(['status' => 'for_sale']);
    }

    public function listAllSold(): array
    {
        return $this->em->getRepository(Vehicle::class)->findBy(['status' => 'sold']);
    }

    public function requestPurchase(Vehicle $vehicle, User $buyer): Vehicle
    {
        if ($vehicle->getStatus() !== 'for_sale') {
            throw new \RuntimeException('Veículo não está disponível para compra.');
        }

        if ($vehicle->getUser() === $buyer) {
            throw new \RuntimeException('Você não pode solicitar seu próprio veículo.');
        }

        $vehicle->setStatus('pending');
        $vehicle->setRequestedBy($buyer);

        $this->em->flush();

        return $vehicle;
    }

    public function acceptPurchaseRequest(Vehicle $vehicle): Vehicle
    {
        if ($vehicle->getStatus() !== 'pending' || !$vehicle->getRequestedBy()) {
            throw new \RuntimeException('Nenhuma solicitação pendente.');
        }

        $vehicle->setUser($vehicle->getRequestedBy());
        $vehicle->setStatus('sold');
        $vehicle->setSoldAt(new \DateTime());
        $vehicle->setRequestedBy(null);

        $this->em->flush();

        return $vehicle;
    }

    public function rejectPurchaseRequest(Vehicle $vehicle): Vehicle
    {
        if ($vehicle->getStatus() !== 'pending') {
            throw new \RuntimeException('Nenhuma solicitação pendente.');
        }

        $vehicle->setStatus('for_sale');
        $vehicle->setRequestedBy(null);

        $this->em->flush();

        return $vehicle;
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
        $year = $this->yearRepo->findOneBy([
            'model'    => $model,
            'fipeCode' => $dto->yearCode
        ]);
        if (!$year) {
            throw new NotFoundHttpException("Ano '{$dto->yearCode}' não encontrado para o modelo.");
        }

        // Busca valor FIPE via API
        $details = $this->fipeApi->getVehicleDetails(
            $brand->getType(),
            $brand->getFipeCode(),
            $model->getFipeCode(),
            $year->getFipeCode()
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
            ->setYear($year)
            ->setFipeValue($fipeValue)
            ->setSalePrice(\number_format((float)$dto->salePrice, 2, '.', ''))
            ->setStatus('for_sale');


        $this->em->persist($vehicle);
        $this->em->flush();

        return $vehicle;
    }

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
