<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateVehicleRequest
{
    #[Assert\NotBlank(message: 'O campo saleprice não pode ficar em branco')]
    public readonly ?float $salePrice;
    #[Assert\NotBlank(message: 'o campo status não pode ficar em branco')]
    #[Assert\Choice(choices: ['for_sale', 'sold'], message: 'O status deve ser "available" ou "sold"')]
    public readonly ?string $status;
}