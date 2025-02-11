<?php

namespace Controllers;

use Core\View;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use PDOException;

class ProductController
{

    private ProductService $productService;

    public function __construct()
    {

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();
        $repository = new ProductRepository($pdo);
        $this->productService = new ProductService($repository);

    }

    public function show($id)
    {

        try 
        {
            $product = $this->productService->getProductByid((int)$id);

            if (!$product) 
            {
                http_response_code(404);
                echo '404 Not Found';
                return;
            }

            $content = View::make(__DIR__ . '/../Views/product/detail.php', [
                'product' => $product,
            ]);

            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                'content' => $content
            ]);
        } 
        catch (PDOException $e) 
        {
            error_log("Database error: " . $e->getMessage());
            echo "Произошла ошибка при загрузке товара.";
        }

    }
    
}