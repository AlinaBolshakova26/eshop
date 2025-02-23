<?php

    namespace Models\Rating;

    final class RatingListDTO
    {
        public function __construct(
            public readonly float $averageRating,
            public readonly int   $totalReviews
        )
        {
        }
    }