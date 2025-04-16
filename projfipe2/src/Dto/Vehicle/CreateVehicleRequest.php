<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVehicleRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(['carros','motos', 'caminhoes'])]
    public readonly string $category;

    #[Assert\NotBlank]
    public readonly string $brand;

    #[Assert\NotBlank]
    public readonly string $model;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(1900)]
    public readonly int $year;
    
    #[Assert\NotBlank]
    #[Assert\Positive]
    public readonly float $salePrice;
}
