<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    #[Assert\Choice(['carros','motos', 'caminhoes'])]
    public readonly ?string $category;

    #[Assert\Positive]
    public readonly ?float $salePrice;

    #[Assert\Choice(['for_sale','sold'])]
    public readonly ?string $status;
}
