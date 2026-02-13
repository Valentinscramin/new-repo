<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use App\Document\Embedded\Specification;

/**
 * @ODM\Document(collection="products")
 * @ODM\UniqueIndex(keys={"slug"=1})
 */
class Product
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") */
    private $name;

    /** @ODM\Field(type="string") */
    private $slug;

    /** @ODM\Field(type="string", nullable=true) */
    private $description;

    /** @ODM\EmbedMany(targetDocument=Specification::class) */
    private $specifications = [];

    /** @ODM\ReferenceOne(targetDocument=Category::class, storeAs="ref") */
    private $category;

    /** @ODM\ReferenceOne(targetDocument=Supplier::class, storeAs="ref") */
    private $supplier;

    /** @ODM\Field(type="date") */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    public function addSpecification(Specification $spec): self
    {
        $this->specifications[] = $spec;
        return $this;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;
        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
