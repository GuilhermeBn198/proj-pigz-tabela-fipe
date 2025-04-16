<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vehicle:read'])]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['vehicle:read'])]
    private ?string $fipeValue = null;
    
    #[ORM\Column(length: 255, columnDefinition: "ENUM('for_sale', 'sold') NOT NULL DEFAULT 'for_sale'")]
    #[Groups(['vehicle:read'])]
    private ?string $status = 'for_sale';
    
    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?Category $category = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?Brand $brand = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?Model $model = null;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?float $salePrice = 0.00; 
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?Year $yearEntity = null;

    public function getSalePrice(): ?string { return $this->salePrice; }
    public function setSalePrice(string $p): static { $this->salePrice = $p; return $this; }
    
    public function getYearEntity(): ?Year { return $this->yearEntity; }
    public function setYearEntity(Year $y): static { $this->yearEntity = $y; return $this; }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFipeValue(): ?string
    {
        return $this->fipeValue;
    }
    
    public function setFipeValue(string $fipeValue): static
    {
        $this->fipeValue = $fipeValue;
        
        return $this;
    }
    
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): static
    {
        $this->status = $status;
        
        return $this;
    }
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): static
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    
    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        
        return $this;
    }
    
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }
    
    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;
        
        return $this;
    }
    
    public function getModel(): ?Model
    {
        return $this->model;
    }
    
    public function setModel(?Model $model): static
    {
        $this->model = $model;
        
        return $this;
    }
}
