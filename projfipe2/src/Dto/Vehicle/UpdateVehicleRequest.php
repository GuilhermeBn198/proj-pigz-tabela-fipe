<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    #[Assert\Choice(['carro','moto'])]
    #[Assert\NotBlank]
    public readonly ?string $category;

    #[Assert\NotBlank]
    public readonly ?string $brand;

    #[Assert\NotBlank]
    public readonly ?string $model;

    #[Assert\GreaterThan(1900)]
    public readonly ?int $year;

    #[Assert\Choice(['for_sale','sold'])]
    public readonly ?string $status;
}
