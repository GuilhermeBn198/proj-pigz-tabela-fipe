<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class TransferVehicleRequest
{
    #[Assert\NotBlank(message: 'O campo newOwnerEmail não pode ficar em branco')]
    #[Assert\Email(message: 'newOwnerEmail deve ser um email válido')]
    public ?string $newOwnerEmail = null;
}