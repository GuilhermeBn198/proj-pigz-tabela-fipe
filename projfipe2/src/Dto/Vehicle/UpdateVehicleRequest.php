<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    public function __construct(
        public readonly ?float $salePrice = null,
        public readonly ?string $status = null
    ) {}
}