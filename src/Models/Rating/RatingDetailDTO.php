<?php

namespace Models\Rating;

class RatingDetailDTO
{
    public int $id;
    public int $userId;
    public float $rating;
    public ?string $comment;
    public string $createdAt;

    public function __construct(int $id, int $userId, float $rating, ?string $comment, string $createdAt)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
    }
}
