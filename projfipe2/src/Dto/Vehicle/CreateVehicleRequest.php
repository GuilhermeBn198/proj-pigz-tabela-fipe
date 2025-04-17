<?php
namespace App\Dto\Vehicle;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVehicleRequest
{
    #[Assert\NotBlank(message: 'O campo category não pode ficar em branco')]
    #[Assert\Choice(['carros','motos','caminhoes'])]
    public readonly string $category;

    #[Assert\NotBlank(message: 'O campo marca não pode ficar em branco')]
    public readonly string $brand;

    #[Assert\NotBlank(message: 'O campo modelo não pode ficar em branco')]
    public readonly string $model;

    #[Assert\NotBlank(message: 'O campo ano não pode ficar em branco')]
    #[Assert\Regex(pattern: '/^\\d{4}-\\d+$/', message: 'yearCode deve seguir o formato YYYY-Código')] 
    public readonly string $yearCode;

    #[Assert\NotBlank(message: 'O campo salePrice não pode ficar em branco')]
    #[Assert\Positive(message: 'salePrice deve ser um valor positivo')]
    #[Assert\Regex(pattern: '/^\\d+(\\.\\d{1,2})?$/', message: 'salePrice deve ser um número com até duas casas decimais')]
    public readonly float $salePrice;
}