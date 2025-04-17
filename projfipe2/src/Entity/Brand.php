<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\VehicleType;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['brand:read'])]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(['brand:read', 'vehicle:read'])]
    private ?string $name = null;
    
    #[ORM\Column(length: 20)]
    #[Groups(['brand:read', 'vehicle:read'])]
    private ?string $fipeCode = null;

    #[ORM\Column(enumType: VehicleType::class)]
    #[Groups(['brand:read'])]
    private VehicleType $type;
    
    /**
    * @var Collection<int, Model>
    */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'brand')]
    private Collection $models;
    
    public function __construct()
    {
        $this->models = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(string $name): static
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getFipeCode(): ?string
    {
        return $this->fipeCode;
    }
    
    public function setFipeCode(string $fipeCode): static
    {
        $this->fipeCode = $fipeCode;
        
        return $this;
    }

    public function getType(): VehicleType
    {
        return $this->type;
    }

    public function setType(VehicleType $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    /**
    * @return Collection<int, Model>
    */
    public function getModels(): Collection
    {
        return $this->models;
    }
    
    public function addModel(Model $model): static
    {
        if (!$this->models->contains($model)) {
            $this->models->add($model);
            $model->setBrand($this);
        }
        
        return $this;
    }
    
    public function removeModel(Model $model): static
    {
        if ($this->models->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getBrand() === $this) {
                $model->setBrand(null);
            }
        }
        
        return $this;
    }
}
