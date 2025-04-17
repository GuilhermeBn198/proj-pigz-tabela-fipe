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
    #[Groups(['vehicle:read', 'user:read'])]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?string $fipeValue = null;
    
    #[ORM\Column(length: 255, columnDefinition: "ENUM('for_sale', 'pending', 'sold') NOT NULL DEFAULT 'for_sale'")]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?string $status = 'for_sale';
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?User $requestedBy = null;
    
    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read'])]
    private ?User $user = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?Brand $brand = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?Model $model = null;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: false)]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?string $salePrice = "0.00"; 
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vehicle:read', 'user:read'])]
    private ?Year $year = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['vehicle:read'])]
    private ?\DateTimeInterface $soldAt = null;

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
    
    public function getSalePrice(): ?string
    {
        return $this->salePrice;
    }
    
    public function setSalePrice(string $p): static
    {
        $this->salePrice = $p;
        return $this;
    }
    
    public function getYear(): ?Year
    {
        return $this->year;
    }
    
    public function setYear(Year $y): static
    {
        $this->year = $y;
        return $this;
    }
    
    public function getSoldAt(): ?\DateTimeInterface
    {
        return $this->soldAt;
    }

    public function setSoldAt(?\DateTimeInterface $soldAt): static
    {
        $this->soldAt = $soldAt;
        return $this;
    }

    public function getRequestedBy(): ?User
    {
        return $this->requestedBy;
    }
    
    public function setRequestedBy(?User $user): static
    {
        $this->requestedBy = $user;
        return $this;
    }

}