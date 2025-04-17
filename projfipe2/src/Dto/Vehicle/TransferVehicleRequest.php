<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class TransferVehicleRequest
{
    #[Assert\NotBlank(message: 'O email não pode ficar em branco')]
    #[Assert\Email(message: 'Formato de email inválido')]
    public readonly string $newOwnerEmail;
}