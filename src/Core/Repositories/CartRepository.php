<?php

namespace Core\Repositories;

use PDO;
use PDOException;
use Models\Cart\Cart;

class CartRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCartItemsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
    SELECT 
        c.*,
        i.name AS product_name,
        i.price AS product_price,
        im.path AS product_image
    FROM up_cart c
    INNER JOIN up_item i ON c.item_id = i.id
    LEFT JOIN up_image im ON i.id = im.item_id AND im.is_main = 1
    WHERE c.user_id = :user_id
    ORDER BY c.created_at DESC
");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];
        foreach ($rows as $row)
        {
            $items[] = Cart::fromDatabase($row);
        }
        return $items;
    }

    public function addItem(int $userId, int $itemId, int $quantity): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT id, quantity 
            FROM up_cart 
            WHERE user_id = :user_id AND item_id = :item_id 
            LIMIT 1
        ");
        $stmt->execute([
            'user_id' => $userId,
            'item_id' => $itemId
        ]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing)
        {
            $newQuantity = (int)$existing['quantity'] + $quantity;
            $updateStmt = $this->pdo->prepare("
                UPDATE up_cart 
                SET quantity = :quantity, updated_at = NOW() 
                WHERE id = :id
            ");
            return $updateStmt->execute([
                'quantity' => $newQuantity,
                'id' => $existing['id']
            ]);
        }
        else
        {
            $insertStmt = $this->pdo->prepare("
                INSERT INTO up_cart (user_id, item_id, quantity, created_at, updated_at)
                VALUES (:user_id, :item_id, :quantity, NOW(), NOW())
            ");
            return $insertStmt->execute([
                'user_id'  => $userId,
                'item_id'  => $itemId,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateItem(int $userId, int $itemId, int $quantity): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE up_cart 
            SET quantity = :quantity, updated_at = NOW() 
            WHERE user_id = :user_id AND item_id = :item_id
        ");
        return $stmt->execute([
            'quantity' => $quantity,
            'user_id'  => $userId,
            'item_id'  => $itemId
        ]);
    }

    public function removeItem(int $userId, int $itemId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM up_cart 
            WHERE user_id = :user_id AND item_id = :item_id
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'item_id' => $itemId
        ]);
    }

    public function clearCart(int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM up_cart 
            WHERE user_id = :user_id
        ");
        return $stmt->execute(['user_id' => $userId]);
    }
}