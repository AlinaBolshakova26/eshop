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

    public function getPaginatedRatings
    (
        int $page,
        int $itemsPerPage
    ): array
    {

        $offset = ($page - 1) * $itemsPerPage;
        $data = $this->repository->findAllPaginated($itemsPerPage, $offset);

        return array_map
        (function($row)
        {
            return new \Models\Rating\AdminRatingDTO
            (
                $row['id'],
                $row['user_name'],
                $row['product_name'],
                $row['rating'],
                $row['comment'],
                $row['created_at']
            );
        }, $data);

    }

    public function getTotalPages(int $itemsPerPage): int
    {

        $totalTags = $this->repository->getTotalCount();

        return ceil($totalTags / $itemsPerPage);

    }

    public function getRatingDetails(int $id): object
    {
        return $this->repository->findFullRatingDetails($id);
    }

	public function hasUserRated(int $userId, int $productId): bool
	{
		return $this->repository->hasUserRated($userId, $productId);
	}

	public function createRating(int $userId, int $productId, int $rating, string $comment): void
	{
		$this->repository->createRating($userId, $productId, $rating, $comment);
	}

    public function deleteRatings(array $ratingIds): bool
    {
        return $this->repository->deleteRatings($ratingIds);
    }
}
