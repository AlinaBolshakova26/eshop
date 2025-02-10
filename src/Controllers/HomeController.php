<?php
namespace Controllers;

use Core\View;
use Core\Services\Product\ProductRepository;
use Core\Services\Product\ProductService;
use Core\Database\MySQLDatabase;

class HomeController
{
    private ProductService $productService;

    private function initialize(): void
    {
        if (!isset($this->productService))
        {
            $database = new MySQLDatabase();
            $pdo = $database->getConnection();

            $repository = new ProductRepository($pdo);
            $this->productService = new ProductService($repository);
        }
    }


    public function index(): void
    {
        $this->initialize();

        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        define("ITEMS_PER_PAGE", 9);

        try {
            $products = $this->productService->getPaginatedProducts($currentPage, ITEMS_PER_PAGE);
            $totalPages = $this->productService->getTotalPages(ITEMS_PER_PAGE);
            
            $content = View::make(
                __DIR__ . "/../Views/home/catalog.php",
                [
                    'products' => $products,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage,
                ]
            );

            echo View::make(
                __DIR__ . '/../Views/layouts/main_template.php',
                [
                    'content' => $content,
                ]
            );
        }
        catch (\PDOException $e)
        {
            error_log("Database error: " . $e->getMessage());
            echo "Произошла ошибка при загрузке товаров.";
        }
    }
}