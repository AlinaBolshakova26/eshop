<?php

namespace Controllers\Admin;

use Core\Services\AdminService;
use Core\Services\OrderService;
use Core\Database\MySQLDatabase;
use Core\Repositories\AdminRepository;
use Core\Repositories\OrderRepository;
use Controllers\Admin\AdminBaseController;

class OrdersAdminController extends AdminBaseController
{

    private AdminService $adminService;
    private OrderService $orderService;

    public function __construct()
    {

        parent::__construct();

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->adminService = new AdminService(new AdminRepository($pdo));
        $this->orderService = new OrderService(new OrderRepository($pdo));

    }

    public function index(): void
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $this->handlePost();
            $this->redirect('/admin/orders');
        }

        $this->showOrderList();

    }

    public function handlePost(): void
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            if (isset($_POST['status'], $_POST['order_id']))
            {
                $this->orderService->changeOrderStatus
                (
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

        $this->redirect('/admin/orders');
    }

    private function showOrderList(): void
    {
        $currentPage = (int)($_GET['page'] ?? 1);
        $itemsPerPage = 30;

        $orders = $this->orderService->getPaginatedOrders($currentPage, $itemsPerPage);
        $totalPages = $this->orderService->getTotalPages($itemsPerPage);

        $this->render
        (
            'admin/orders/index', 
            [
                'orders' => $orders,
                'totalPages' => $totalPages,
                'currentPage' => $currentPage
            ]
        );
    }
}
