<?php

namespace Controllers\Admin;

use Core\Services\OrderService;
use Core\Database\MySQLDatabase;
use Core\Repositories\OrderRepository;
use Controllers\Admin\AdminBaseController;

class OrderDetailAdminController extends AdminBaseController
{

    private OrderService $orderService;

    public function __construct()
    {

        parent::__construct();

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();
        $this->orderService = new OrderService(new OrderRepository($pdo));

    }

    public function show(int $id): void
    {

        $order = $this->orderService->getOrderById($id);

        if (!$order)
        {
            $this->redirect('/admin/orders');
        }

        $this->render
        (
            'admin/orders/detail', 
            [
                'order' => $order
            ]
        );
        
    }
    
}
