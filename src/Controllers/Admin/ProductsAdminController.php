<?php

namespace Controllers\Admin;

use Core\View;
use Core\Services\AdminService;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\AdminRepository;
use Core\Repositories\ProductRepository;

class ProductsAdminController
{

    private AdminService $adminService;
    private ProductService $productService;

	public function __construct()
	{
		$database = new MySQLDatabase();
		$pdo = $database->getConnection();

		$this->adminService = new AdminService(new AdminRepository($pdo));
		$this->productService = new ProductService(new ProductRepository($pdo));
	}

    public function index(): void
    {

        if (!$this->adminService->isAdminLoggedIn())
        {
            header('Location: /admin/login');
            exit;
        }

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        define("ITEMS_PER_PAGE", 30);

        try 
        {
            $products = $this->productService->adminGetPaginatedProducts($currentPage, ITEMS_PER_PAGE, false);
            $totalPages = $this->productService->getTotalPages(ITEMS_PER_PAGE);

            $content = View::make(__DIR__ . '/../../Views/admin/products/index.php', [
                'products' => $products,
                'totalPages' => $totalPages,
                'currentPage' => $currentPage,
                'error' => View::make(__DIR__ . '/../../Views/admin/error_block.php')
            ]);

            echo View::make(__DIR__ . '/../../Views/layouts/admin_layout.php', [
                'content' => $content,
            ]);
        }
        catch (\PDOException $e)
        {
            error_log("Database error: " . $e->getMessage());
            echo "Произошла ошибка при загрузке товаров.";
        }

    }

    public function process(): void
    {

        if (!$this->adminService->isAdminLoggedIn()) 
        {
            header('Location: /admin/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $selectedProducts = $_POST['selected_products'] ?? [];
            $action = $_POST['action'] ?? '';

            if (empty($selectedProducts)) 
            {
                header('Location: /admin/products?error=no_products_selected');
                exit;
            }

            try 
            {
                $productIds = array_map('intval', $selectedProducts);

                switch ($action) 
                {
                    case 'deactivate':
                        $this->productService->adminToggleStatus($productIds, false);
                        break;
                    case 'activate':
                        $this->productService->adminToggleStatus($productIds,  true);
                        break;
                    default:
                        header('Location: /admin/products?error=invalid_action');
                        exit;
                }

                header('Location: /admin/products?success=1');
                exit;
            }
            catch (\PDOException $e)
            {
                error_log("Database error: " . $e->getMessage());
                header('Location: /admin/products?error=database_error');
                exit;
            }
        }

        header('Location: /admin/products');
        exit;

    }
    
}