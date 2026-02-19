<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ODM\Document(collection="saved_comparisons")
 */
class SavedComparison
{
    /** @ODM\Id */
    private $id;

    /** @ODM\ReferenceOne(targetDocument=User::class, storeAs="ref") */
    private $user;

    /** @ODM\ReferenceMany(targetDocument=Product::class, storeAs="ref") */
    private $products;

    /** 
     * @ODM\ReferenceOne(targetDocument=Product::class, storeAs="ref") 
     * @deprecated Use products array instead
     */
    private $productA;

    /** 
     * @ODM\ReferenceOne(targetDocument=Product::class, storeAs="ref") 
     * @deprecated Use products array instead
     */
    private $productB;

    /** @ODM\Field(type="date_immutable") */
    private $createdAt;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);
        return $this;
    }

    public function setProducts(array $products): self
    {
        $this->products = new ArrayCollection($products);
        return $this;
    }

    // Legacy methods for backward compatibility
    public function getProductA(): ?Product
    {
        return $this->productA;
    }

    public function setProductA(Product $product): self
    {
        $this->productA = $product;
        return $this;
    }

    public function getProductB(): ?Product
    {
        return $this->productB;
    }

    public function setProductB(Product $product): self
    {
        $this->productB = $product;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
