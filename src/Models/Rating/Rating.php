<?php

namespace Models\Rating;

class Rating
{
    private int $id;
    private int $productId;
    private int $userId;
    private float $rating;
    private string $comment;
    private \DateTime $createdAt;

    public function __construct(int $id, int $productId, int $userId, float $rating, string $comment, \DateTime $createdAt)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->userId = $userId;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toDTO(): RatingDTO
    {
        return new RatingDTO(
            $this->id,
            $this->productId,
            $this->userId,
            $this->rating,
            $this->comment,
            $this->createdAt->format('Y-m-d H:i:s')
        );
    }
}