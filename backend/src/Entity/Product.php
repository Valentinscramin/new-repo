<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Comparison::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comparison $comparison = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $currency = 'USD';

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $specs = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $strengths = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $weaknesses = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $score = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $rawExtraction = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComparison(): ?Comparison
    {
        return $this->comparison;
    }

    public function setComparison(?Comparison $comparison): static
    {
        $this->comparison = $comparison;

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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSpecs(): ?array
    {
        return $this->specs;
    }

    public function setSpecs(?array $specs): static
    {
        $this->specs = $specs;

        return $this;
    }

    public function getStrengths(): ?array
    {
        return $this->strengths;
    }

    public function setStrengths(?array $strengths): static
    {
        $this->strengths = $strengths;

        return $this;
    }

    public function getWeaknesses(): ?array
    {
        return $this->weaknesses;
    }

    public function setWeaknesses(?array $weaknesses): static
    {
        $this->weaknesses = $weaknesses;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getRawExtraction(): ?array
    {
        return $this->rawExtraction;
    }

    public function setRawExtraction(?array $rawExtraction): static
    {
        $this->rawExtraction = $rawExtraction;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }
}
