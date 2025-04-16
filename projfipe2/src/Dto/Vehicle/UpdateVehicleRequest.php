<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    #[Assert\Positive]
    public readonly ?float $salePrice = null;

    #[Assert\Choice(['for_sale','sold'])]
    public readonly ?string $status = null;
}