<?php
namespace Core\Services\Order;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getPaginatedOrders(int $page, int $itemsPerPage): array
    {
        if ($page < 1 || $itemsPerPage < 1) {
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
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Неправильный статус");
        }
        return $this->orderRepository->changeOrderStatus($orderId, $status);
    }


    public function addOrder(int $userId, int $itemId, float $price, string $address): bool
    {
        if ($price <= 0 || empty($address)) {
            throw new \InvalidArgumentException("Неправильные данные");
        }
        return $this->orderRepository->addOrder($userId, $itemId, $price, $address);
    }
}