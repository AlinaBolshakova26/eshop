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
        catch (PDOException $e)
        {
            error_log("Ошибка отмены заказов: " . $e->getMessage());

            return false;
        }

    }

    public function getOrderById(int $id): ?array
    {

        $stmt = $this->pdo->prepare("SELECT o.id, o.user_id, o.item_id, o.price, o.status, o.created_at,
        o.updated_at, o.address, u.phone, u.name, u.email FROM up_order o 
        INNER JOIN up_user u on o.user_id = u.id WHERE o.id = :id LIMIT 1");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;

    }

}