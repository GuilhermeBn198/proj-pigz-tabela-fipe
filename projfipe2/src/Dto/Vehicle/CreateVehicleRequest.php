<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVehicleRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(['carro','moto'])]
        public readonly string $category,

        #[Assert\NotBlank]
        public readonly string $brand,

        #[Assert\NotBlank]
        public readonly string $model,

        #[Assert\NotBlank]
        #[Assert\GreaterThan(1900)]
        public readonly int $year,
    ) {}
}
