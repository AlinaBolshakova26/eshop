<?php
namespace Controllers\Admin;

use Core\View;
use Core\Services\Order\OrderService;
use Core\Database\MySQLDatabase;
use Core\Services\Order\OrderRepository;

class OrderDetailAdminController
{
    private OrderService $orderService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();
        $this->orderService = new OrderService(new OrderRepository($pdo));
    }

    public function show(int $id): void
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order)
        {
            header('Location: /admin/orders');
            exit;
        }

        $content = View::make(__DIR__ . '/../../Views/admin/orders/detail.php', [
            'order' => $order
        ]);

        echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', [
            'content' => $content,
        ]);
    }
}
