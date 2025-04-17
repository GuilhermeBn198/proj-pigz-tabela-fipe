<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVehicleRequest
{
    #[Assert\NotBlank(message: 'O campo category não pode ficar em branco')]
    public ?string $category = null;

    #[Assert\NotBlank(message: 'O campo brand não pode ficar em branco')]
    public ?string $brand = null;

    #[Assert\NotBlank(message: 'O campo model não pode ficar em branco')]
    public ?string $model = null;

    #[Assert\NotBlank(message: 'O campo yearCode não pode ficar em branco')]
    #[Assert\Regex(
        pattern: '/^\d{4}-\d+$/',
        message: 'yearCode deve seguir o formato YYYY-Código'
    )]
    public ?string $yearCode = null;

    #[Assert\NotBlank(message: 'O campo salePrice não pode ficar em branco')]
    #[Assert\Positive(message: 'salePrice deve ser um número positivo')]
    public ?float $salePrice = null;
}