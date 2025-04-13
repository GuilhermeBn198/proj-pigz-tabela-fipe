<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class VehicleUpdateRequest
{
    public function __construct(
        #[Assert\Choice(['carro','moto'])]
        public readonly ?string $category = null,

        public readonly ?string $brand = null,

        public readonly ?string $model = null,

        #[Assert\GreaterThan(1900)]
        public readonly ?int $year = null,

        #[Assert\Choice(['for_sale','sold'])]
        public readonly ?string $status = null,
    ) {}
}
