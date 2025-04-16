<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    public readonly ?float $salePrice;
    public readonly ?string $status;
}