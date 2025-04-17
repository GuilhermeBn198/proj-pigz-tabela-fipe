<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    #[Assert\NotBlank(message: 'O campo saleprice não pode ficar em branco')]
    public ?float $salePrice = null;
    #[Assert\NotBlank(message: 'o campo status não pode ficar em branco')]
    #[Assert\Choice(choices: ['for_sale', 'sold'], message: 'O status deve ser "available" ou "sold"')]
    public ?string $status = null;
}