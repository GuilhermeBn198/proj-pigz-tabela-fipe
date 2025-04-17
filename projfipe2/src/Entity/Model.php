<?php

namespace App\Entity;

use App\Repository\ModelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Year;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model
{
    public function __construct()
    {
        $this->years = new ArrayCollection();
    }
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['model:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['model:read', 'vehicle:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Groups(['model:read', 'vehicle:read'])]
    private ?string $fipeCode = null;

    #[ORM\ManyToOne(inversedBy: 'models')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['model:read'])]
    private ?Brand $brand = null;
 
    /**
     * @var Collection<int, Year>
     */
    #[ORM\OneToMany(targetEntity: Year::class, mappedBy: 'model', cascade: ['persist','remove'])]
    private Collection $years;
 
    /**
     * @return Collection<int, Year>
     */
    public function getYears(): Collection
    {
        return $this->years;
    }

    public function addYear(Year $year): static
    {
        if (!$this->years->contains($year)) {
            $this->years->add($year);
            $year->setModel($this);
        }
        return $this;
    }

    public function removeYear(Year $year): static
    {
        if ($this->years->removeElement($year)) {
            if ($year->getModel() === $this) {
                $year->setModel(null);
            }
        }
        return $this;
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

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }
}
