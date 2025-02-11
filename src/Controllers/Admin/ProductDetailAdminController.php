<?php

namespace Controllers\Admin;

use Core\View;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;

class ProductDetailAdminController
{

    private ProductService $productService;

    public function __construct()
    {

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();
        $this->productService = new ProductService(new ProductRepository($pdo));

    }

    public function edit(int $id): void
    {

        $product = $this->productService->adminGetProductByid($id);
                
        if (!$product)
        {
            header('Location: /admin/products');
            exit;
        }

        $content = View::make(__DIR__ . '/../../Views/admin/products/detail.php', 
    [
                'product' => $product
            ]
        );

        echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', 
    [
                'content' => $content,
            ]
        );

    }
    
}