<?php
// src/Service/VehicleSupportService.php
namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use App\Repository\YearRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleSupportService
{
    public function __construct(
        private CategoryRepository $categoryRepo,
        private BrandRepository    $brandRepo,
        private ModelRepository    $modelRepo,
        private YearRepository     $yearRepo
    ) {}

    public function listAllCategories(): array
    {
        return $this->categoryRepo->findAll();
    }

    public function listAllBrands(): array
    {
        return $this->brandRepo->findAll();
    }

    public function listModelsByBrand(string $brandFipeCode): array
    {
        $brand = $this->brandRepo->findOneBy(['fipeCode' => $brandFipeCode]);
        if (!$brand) {
            throw new NotFoundHttpException("Marca FIPE '{$brandFipeCode}' não encontrada.");
        }
        return $this->modelRepo->findBy(['brand' => $brand]);
    }

    public function listYearsByModel(string $modelFipeCode): array
    {
        $model = $this->modelRepo->findOneBy(['fipeCode' => $modelFipeCode]);
        if (!$model) {
            throw new NotFoundHttpException("Modelo FIPE '{$modelFipeCode}' não encontrado.");
        }
        return $this->yearRepo->findBy(['model' => $model]);
    }
}
