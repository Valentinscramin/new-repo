<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="product_offers")
 * @ODM\Index(keys={"price"=1})
 */
class ProductOffer
{
    /** @ODM\Id */
    private $id;

    /** @ODM\ReferenceOne(targetDocument=Product::class, storeAs="ref") */
    private $product;

    /** @ODM\ReferenceOne(targetDocument=Marketplace::class, storeAs="ref") */
    private $marketplace;

    /** @ODM\Field(type="float") */
    private $price;

    /** @ODM\Field(type="string") */
    private $affiliateLink;

    /** @ODM\Field(type="date_immutable") */
    private $lastUpdatedAt;

    public function __construct()
    {
        $this->lastUpdatedAt = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setMarketplace(Marketplace $m): self
    {
        $this->marketplace = $m;
        return $this;
    }

    public function getMarketplace(): ?Marketplace
    {
        return $this->marketplace;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setAffiliateLink(string $link): self
    {
        $this->affiliateLink = $link;
        return $this;
    }

    public function getAffiliateLink(): ?string
    {
        return $this->affiliateLink;
    }

    public function getLastUpdatedAt(): \DateTimeImmutable
    {
        return $this->lastUpdatedAt;
    }

    public function touch(): self
    {
        $this->lastUpdatedAt = new \DateTimeImmutable();
        return $this;
    }
}
