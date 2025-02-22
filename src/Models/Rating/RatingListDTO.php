<?php

namespace Models\Rating;

class RatingListDTO {
    public float $averageRating;
    public int $totalReviews;

    public function __construct(float $averageRating, int $totalReviews) {
        $this->averageRating = $averageRating;
        $this->totalReviews = $totalReviews;
    }
}