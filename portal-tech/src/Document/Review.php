<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="reviews")
 */
class Review
{
    /** @ODM\Id */
    private $id;

    /** @ODM\ReferenceOne(targetDocument=User::class, storeAs="ref") */
    private $user;

    /** @ODM\ReferenceOne(targetDocument=Product::class, storeAs="ref") */
    private $product;

    /** @ODM\Field(type="int") */
    private $rating;

    /** @ODM\Field(type="string") */
    private $comment;

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

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
