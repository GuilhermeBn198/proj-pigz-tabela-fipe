<?php

namespace App\Entity;

use App\Repository\YearRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Model;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: YearRepository::class)]
class Year
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fipe:year'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fipe:year'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fipe:year'])]
    private ?string $fipeCode = null;

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'years')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['fipe:year'])]
    private ?Model $model = null;

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFipeCode(): ?string
    {
        return $this->fipeCode;
    }

    public function setFipeCode(string $code): static
    {
        $this->fipeCode = $code;
        return $this;
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
}
