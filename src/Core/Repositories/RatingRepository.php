<?php

namespace Core\Repositories;

use PDO;
use Models\Rating\RatingListDTO;
use Models\Rating\Rating;

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

    public function createRating(int $userId, int $productId, int $rating): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO up_ratings (user_id, product_id, rating) 
        VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $productId, $rating]);
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

    public function findAllPaginated(int $limit, int $offset): array
    {
        $sql = "
        SELECT 
            r.id,
            r.product_id,
            r.user_id,
            r.rating,
            r.comment,
            r.created_at,
            u.name AS user_name,
            i.name AS product_name
        FROM up_ratings r
        INNER JOIN up_user u ON u.id = r.user_id
        INNER JOIN up_item i ON i.id = r.product_id
    ";

        $params = [];

        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':limit', $params[':limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $params[':offset'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount(): int
    {
        $sql = "SELECT COUNT(*)
            FROM up_ratings r
            INNER JOIN up_user u ON u.id = r.user_id
            INNER JOIN up_item i ON i.id = r.product_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function findFullRatingDetails(int $id): ?object
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            r.id,
            r.rating,
            r.comment,
            r.created_at AS createdAt,
            u.id AS userId,
            u.name AS userName,
            u.email AS userEmail,
            u.phone AS userPhone,
            i.id AS productId,
            i.name AS productName,
            o.id AS orderId,
            o.created_at AS orderDate
        FROM up_ratings r
        LEFT JOIN up_user u ON u.id = r.user_id
        LEFT JOIN up_item i ON i.id = r.product_id
        LEFT JOIN up_order o ON o.item_id = r.product_id AND o.user_id = r.user_id
        WHERE r.id = ?
    ");

        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (object)$result : null;
    }
}