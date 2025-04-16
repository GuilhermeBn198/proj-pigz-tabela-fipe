<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class TransferVehicleRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $newOwnerEmail
    ) {}
}