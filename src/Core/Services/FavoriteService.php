<?php
namespace Core\Services;

use Core\Repositories\FavoriteRepository;

class FavoriteService {

    private FavoriteRepository $favoriteRepository;

    public function __construct(FavoriteRepository $favoriteRepository) 
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function toggleFavorite(int $userId, int $itemId): bool 
    {

        if ($this->favoriteRepository->isFavorite($userId, $itemId)) 
        {
            return $this->favoriteRepository->removeFavorite($userId, $itemId);
        }

        return $this->favoriteRepository->addFavorite($userId, $itemId);

    }

    public function removeFavorite(int $userId, int $itemId): bool 
    {
        return $this->favoriteRepository->removeFavorite($userId, $itemId);
    }

    public function getFavorites(int $userId): array 
    {
        return $this->favoriteRepository->getFavoritesByUserId($userId);
    }

}

