<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class TransferVehicleRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public readonly string $newOwnerEmail;
}