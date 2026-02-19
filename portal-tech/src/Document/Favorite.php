<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="favorites")
 */
class Favorite
{
    /** @ODM\Id */
    private $id;

    /** @ODM\ReferenceOne(targetDocument=User::class, storeAs="ref") */
    private $user;

    /** @ODM\ReferenceOne(targetDocument=Product::class, storeAs="ref") */
    private $product;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }
}
