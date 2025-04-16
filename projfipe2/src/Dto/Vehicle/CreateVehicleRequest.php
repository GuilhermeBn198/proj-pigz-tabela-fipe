<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVehicleRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(['carros','motos','caminhoes'])]
    public readonly string $category;

    #[Assert\NotBlank]
    public readonly string $brand;

    #[Assert\NotBlank]
    public readonly string $model;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\\d{4}-\\d+$/', message: 'yearCode deve seguir o formato YYYY-Código')] 
    public readonly string $yearCode;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public readonly float $salePrice;
}