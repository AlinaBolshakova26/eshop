<?php

namespace Core\Services;

use Core\Repositories\RatingRepository;
use Models\Rating\RatingListDTO;

class RatingService
{
    private RatingRepository $repository;

    public function __construct(RatingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRatingsForProducts(array $productIds): array
    {
        return $this->repository->getAverageRatingsForProducts($productIds);
    }

    public function getProductRating(int $productId): RatingListDTO
    {
        $ratings = $this->repository->getAverageRatingsForProducts([$productId]);
        return $ratings[$productId] ?? new RatingListDTO(0, 0);
    }

}
