<?php
namespace Models\Rating;

class AdminRatingDTO
{
    public function __construct(
        public int $id,
        public string $userName,
        public string $productName,
        public float $rating,
        public ?string $comment,
        public string $createdAt
    ) {}
}