<?php
// src/Controller/VehicleSupportController.php
namespace App\Controller;

use App\Service\VehicleSupportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api')]
class VehicleSupportController extends AbstractController
{
    public function __construct(private VehicleSupportService $support) {}

    #[Route('/categories', methods: ['GET'])]
    public function categories(): JsonResponse
    {
        $data = $this->support->listAllCategories();
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'category:read']);
    }

    #[Route('/brands', methods: ['GET'])]
    public function brands(): JsonResponse
    {
        $data = $this->support->listAllBrands();
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'brand:read']);
    }

    #[Route('/brands/{brandFipeCode}/models', methods: ['GET'])]
    public function modelsByBrand(string $brandFipeCode): JsonResponse
    {
        $data = $this->support->listModelsByBrand($brandFipeCode);
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'model:read']);
    }

    #[Route('/models/{modelFipeCode}/years', methods: ['GET'])]
    public function yearsByModel(string $modelFipeCode): JsonResponse
    {
        $data = $this->support->listYearsByModel($modelFipeCode);
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'year:read']);
    }
}
