<?php

namespace Core\Repositories;

use PDO;
use PDOException;

class OrderRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPaginatedOrders(int $page, int $itemsPerPage): array
    {

        $offset = ($page - 1) * $itemsPerPage;

        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM up_order 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getTotalPages(int $itemsPerPage): int
    {

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM up_order");

        $stmt->execute();
        $totalOrders = $stmt->fetchColumn();

        return (int)ceil($totalOrders / $itemsPerPage);

    }

    public function changeOrderStatus(int $orderId, string $status): bool
    {

        try {
            $stmt = $this->pdo->prepare("UPDATE up_order SET status = :status WHERE id = :id");

            $stmt->execute(['id' => $orderId, 'status' => $status]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Ошибка изменения статуса: " . $e->getMessage());

            return false;
        }

    }

    public function cancelOrders(array $orderIds): bool
    {

        try {
            if (empty($orderIds)) {
                throw new \InvalidArgumentException("Не выбраны заказы для отмены");
            }

            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

            $stmt = $this->pdo->prepare("
                UPDATE up_order 
                SET status = 'Отменен' 
                WHERE id IN ($placeholders)
            ");

            return $stmt->execute($orderIds);
        } catch (PDOException $e) {
            error_log("Ошибка отмены заказов: " . $e->getMessage());

            return false;
        }

    }

    public function getOrderById(int $id): ?array
    {

        $stmt = $this->pdo->prepare("SELECT o.id, o.user_id, o.item_id, o.price, o.status, o.created_at,
        o.updated_at, o.city, o.street, o.house, o.apartment, u.phone, u.name, u.email, i.name as item_name FROM up_order o 
        INNER JOIN up_user u on o.user_id = u.id  
        INNER JOIN up_item i on o.item_id = i.id 
        WHERE o.id = :id LIMIT 1");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;

    }

    public function getProductById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT i.*, im.path AS main_image 
            FROM up_item i 
            LEFT JOIN up_image im ON i.id = im.item_id AND im.is_main = 1 
            WHERE i.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByData(string $phone, string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM up_user WHERE phone = :phone OR email = :email
        ");
        $stmt->execute(['phone' => $phone, 'email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createNewUser(string $name, string $phone, string $email): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO up_user (name, phone, email, role) VALUES (:name, :phone, :email, 'customer')
        ");
        $stmt->execute(['name' => $name, 'phone' => $phone, 'email' => $email]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getPrice(int $productId): float
    {
        $stmt = $this->pdo->prepare("SELECT price FROM up_item WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['price'] : 0.0;
    }

    public function saveOrder(int $user_id, int $item_id, float $price, string $city, string $street, string $house, ?string $apartment): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO up_order (user_id, item_id, price, city, street, house, apartment, status) 
                VALUES (:user_id, :item_id, :price, :city, :street, :house, :apartment, 'Создан')
            ");
            return $stmt->execute([
                'user_id' => $user_id,
                'item_id' => $item_id,
                'price' => $price,
                'city' => $city,
                'street' => $street,
                'house' => $house,
                'apartment' => $apartment
            ]);
        }
        catch (PDOException $e)
        {
            error_log("Ошибка сохранения заказа: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT name, phone, email FROM up_user WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getOrdersByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            o.id AS order_id,
            o.status,
            o.created_at,
            i.id AS product_id,
            i.name AS product_name,
            i.price,
            im.path AS main_image
        FROM up_order o
        INNER JOIN up_item i ON o.item_id = i.id
        LEFT JOIN up_image im ON i.id = im.item_id AND im.is_main = 1
        WHERE o.user_id = :user_id
        ORDER BY o.created_at DESC
    ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

}

