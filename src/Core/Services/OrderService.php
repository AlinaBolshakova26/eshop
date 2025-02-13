<?php

namespace Core\Services;

use Core\Repositories\OrderRepository;
class OrderService
{

    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {

        $this->orderRepository = $orderRepository;

    }

    public function getPaginatedOrders(int $page, int $itemsPerPage): array
    {

        if ($page < 1 || $itemsPerPage < 1) 
        {
            throw new \InvalidArgumentException("Неправильные параметры пагинации");
        }

        return $this->orderRepository->getPaginatedOrders($page, $itemsPerPage);

    }

    public function cancelOrders(array $orderIds): bool
    {
        return $this->orderRepository->cancelOrders($orderIds);
    }

    public function getTotalPages(int $itemsPerPage): int
    {
        return $this->orderRepository->getTotalPages($itemsPerPage);
    }

    public function changeOrderStatus(int $orderId, string $status): bool
    {

        $allowedStatuses = ['Создан', 'В пути', 'Доставлен', 'Отменен'];

        if (!in_array($status, $allowedStatuses)) 
        {
            throw new \InvalidArgumentException("Неправильный статус");
        }

        return $this->orderRepository->changeOrderStatus($orderId, $status);
        
    }

    public function getOrderById(int $id): ?array
    {
        return $this->orderRepository->getOrderById($id);
    }

    public function getOrdersByUserId(int $userId): array
    {
        return $this->orderRepository->getOrdersByUserId($userId);
    }
}