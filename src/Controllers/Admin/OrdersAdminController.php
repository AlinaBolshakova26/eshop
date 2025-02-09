<?php
namespace Controllers\Admin;

use Core\View;
use Core\Services\Admin\AdminService;
use Core\Services\Order\OrderService;
use Core\Database\MySQLDatabase;
use Core\Services\Admin\AdminRepository;
use Core\Services\Order\OrderRepository;

class OrdersAdminController
{
    private AdminService $adminService;
    private OrderService $orderService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->adminService = new AdminService(new AdminRepository($pdo));
        $this->orderService = new OrderService(new OrderRepository($pdo));
    }

    public function index(): void
    {
        if (!$this->adminService->isAdminLoggedIn()) {
            header('Location: /admin/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
            header('Location: /admin/orders');
            exit;
        }

        $this->showOrderList();
    }

    public function handlePost(): void
    {
        if (!$this->adminService->isAdminLoggedIn())
        {
            header('Location: /admin/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Смена статуса для конкретного заказа
            if (isset($_POST['status'], $_POST['order_id']))
            {
                $this->orderService->changeOrderStatus(
                    (int)$_POST['order_id'],
                    $_POST['status']
                );
            }

            if (isset($_POST['cancel_order_ids']))
            {
                $orderIds = array_map('intval', $_POST['cancel_order_ids']);
                $this->orderService->cancelOrders($orderIds);
            }
        }

        header('Location: /admin/orders');
        exit;
    }

    private function showOrderList(): void
    {
        try {
            $currentPage = (int)($_GET['page'] ?? 1);
            $itemsPerPage = 30;

            $orders = $this->orderService->getPaginatedOrders($currentPage, $itemsPerPage);
            $totalPages = $this->orderService->getTotalPages($itemsPerPage);

            $content = View::make(__DIR__ . '/../../Views/admin/orders/index.php', [
                'orders' => $orders,
                'totalPages' => $totalPages,
                'currentPage' => $currentPage
            ]);

            echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', [
                'content' => $content,
            ]);

        } catch (\Exception $e) {
            View::make('error.php', ['message' => 'Произошла ошибка при загрузке заказов']);
        }
    }
}