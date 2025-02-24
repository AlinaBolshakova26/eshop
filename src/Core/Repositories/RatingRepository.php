<?php

namespace Core\Repositories;

use PDO;
use Models\Rating\RatingListDTO;

class RatingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAverageRatingsForProducts(array $productIds): array
    {
        if (empty($productIds))
        {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "SELECT 
                product_id,
                AVG(rating) AS average_rating,
                COUNT(id) AS total_reviews 
              FROM up_ratings 
              WHERE product_id IN ($placeholders)
              GROUP BY product_id";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($productIds);

        $ratings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $ratings[$row['product_id']] = new RatingListDTO(
                (float) $row['average_rating'],
                (int) $row['total_reviews']
            );
        }

        return $ratings;
    }

    public function getRatingByUserAndProduct(int $userId, int $productId): ?int
    {
        $stmt = $this->pdo->prepare("SELECT rating FROM up_ratings WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['rating'] : null;
    }

    public function createRating(int $userId, int $productId, int $rating, string $comment): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO up_ratings (user_id, product_id, rating, comment) 
        VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $productId, $rating, $comment]);
    }

    public function hasUserRated(int $userId, int $productId): bool
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) 
        FROM up_ratings 
        WHERE user_id = :user_id 
        AND product_id = :product_id
    ");
        $stmt->execute([
            ':user_id' => $userId,
            ':product_id' => $productId
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function getUserRatingsForProducts(int $userId, array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $query = "SELECT 
                product_id,
                rating as value,
                comment,
                1 as rated
              FROM up_ratings 
              WHERE user_id = ? AND product_id IN ($placeholders)";

        $stmt = $this->pdo->prepare($query);
        $params = array_merge([$userId], $productIds);
        $stmt->execute($params);

        $ratings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ratings[$row['product_id']] = [
                'value' => (int)$row['value'],
                'comment' => $row['comment'],
                'rated' => (bool)$row['rated']
            ];
        }

        // Заполняем массив для продуктов без рейтингов
        foreach ($productIds as $productId) {
            if (!isset($ratings[$productId])) {
                $ratings[$productId] = [
                    'value' => 0,
                    'comment' => '',
                    'rated' => false
                ];
            }
        }

        return $ratings;
    }
    
}