<?php
namespace Core\Repositories;

use PDO;
use Models\Favorite\FavoriteListDTO;

class FavoriteRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function addFavorite(int $userId, int $itemId): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO up_favorites (user_id, item_id)
            VALUES (:user_id, :item_id)
        ");
        return $stmt->execute(['user_id' => $userId, 'item_id' => $itemId]);
    }

    public function removeFavorite(int $userId, int $itemId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM up_favorites 
            WHERE user_id = :user_id AND item_id = :item_id
        ");
        return $stmt->execute(['user_id' => $userId, 'item_id' => $itemId]);
    }

    public function isFavorite(int $userId, int $itemId): bool {
        $stmt = $this->pdo->prepare("
            SELECT id FROM up_favorites 
            WHERE user_id = :user_id AND item_id = :item_id
        ");
        $stmt->execute(['user_id' => $userId, 'item_id' => $itemId]);
        return (bool)$stmt->fetch();
    }

    public function getFavoritesByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT f.*, i.name AS product_name, i.price AS product_price, im.path AS main_image
            FROM up_favorites f
            INNER JOIN up_item i ON f.item_id = i.id
            LEFT JOIN up_image im ON i.id = im.item_id AND im.is_main = 1
            WHERE f.user_id = :user_id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $favorites = [];
        foreach ($results as $row) {
            $favorites[] = new FavoriteListDTO($row);
        }
        return $favorites;
    }
}
