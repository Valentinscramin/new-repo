<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="marketplaces")
 * @ODM\UniqueIndex(keys={"slug"=1})
 */
class Marketplace
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") */
    private $name;

    /** @ODM\Field(type="string") */
    private $slug;

    /** @ODM\Field(type="string") */
    private $affiliateBaseUrl;

    /** @ODM\Field(type="string", nullable=true) */
    private $logo;

    /** @ODM\Field(type="date_immutable") */
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

    public function getAffiliateBaseUrl(): ?string
    {
        return $this->affiliateBaseUrl;
    }

    public function setAffiliateBaseUrl(string $url): self
    {
        $this->affiliateBaseUrl = $url;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
