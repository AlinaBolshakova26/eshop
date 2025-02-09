<?php
namespace Core\Services\Order;

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
        return (int) ceil($totalOrders / $itemsPerPage);
    }

    public function changeOrderStatus(int $orderId, string $status): bool
    {
        try
        {
            $stmt = $this->pdo->prepare("UPDATE up_order SET status = :status WHERE id = :id");
            $stmt->execute(['id' => $orderId, 'status' => $status]);
            return $stmt->rowCount() > 0;
        }
        catch (PDOException $e)
        {
            error_log("Ошибка изменения статуса: " . $e->getMessage());
            return false;
        }
    }

    public function cancelOrders(array $orderIds): bool
    {
        try
        {
            if (empty($orderIds))
            {
                throw new \InvalidArgumentException("Не выбраны заказы для отмены");
            }
            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
            $stmt = $this->pdo->prepare("
                UPDATE up_order 
                SET status = 'Отменен' 
                WHERE id IN ($placeholders)
            ");
            return $stmt->execute($orderIds);
        }
        catch (\PDOException $e)
        {
            error_log("Ошибка отмены заказов: " . $e->getMessage());
            return false;
        }
    }

    public function addOrder(int $userId, int $itemId, float $price, string $address): bool
    {
        try
        {
            $stmt = $this->pdo->prepare("INSERT INTO up_order 
                (user_id, item_id, price, address, status) 
                VALUES (:user_id, :item_id, :price, :address, 'Создан')");

            return $stmt->execute([
                'user_id' => $userId,
                'item_id' => $itemId,
                'price' => $price,
                'address' => $address
            ]);
        }
        catch (PDOException $e)
        {
            error_log("Ошибка при добавлении заказа: " . $e->getMessage());
            return false;
        }
    }
}