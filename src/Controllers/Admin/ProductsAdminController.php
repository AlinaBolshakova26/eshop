<?php
namespace Controllers\Admin;

use Core\View;
use Core\Services\Admin\AdminService;
use Core\Services\Product\ProductService;
use Core\Database\MySQLDatabase;
use Core\Services\Admin\AdminRepository;
use Core\Services\Product\ProductRepository;

class ProductsAdminController
{
    private AdminService $adminService;
    private ProductService $productService;

    private function initialize(): void
    {
        if (!isset($this->productService) || !isset($this->adminService) || !isset($this->productRepository))
        {
            $database = new MySQLDatabase();
            $pdo = $database->getConnection();

            if (!isset($this->adminService))
            {
                $adminRepository = new AdminRepository($pdo);
                $this->adminService = new AdminService($adminRepository);
            }

            if (!isset($this->productService)) {
                $productRepository = new ProductRepository($pdo);
                $this->productService = new ProductService($productRepository);
            }
        }

    }
    public function index(): void
    {
        $this->initialize();

        if (!$this->adminService->isAdminLoggedIn())
        {
            header('Location: /admin/login');
            exit;
        }

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        define("ITEMS_PER_PAGE", 30);

        try {

            $products = $this->productService->adminGetPaginatedProducts($currentPage, ITEMS_PER_PAGE);
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
        $this->initialize();

        if (!$this->adminService->isAdminLoggedIn()) {
            header('Location: /admin/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedProducts = $_POST['selected_products'] ?? [];
            $action = $_POST['action'] ?? '';

            if (empty($selectedProducts)) {
                // Если товары не выбраны, перенаправляем обратно с сообщением об ошибке
                header('Location: /admin/products?error=no_products_selected');
                exit;
            }

            try {

                $productIds = array_map('intval', $selectedProducts);

                switch ($action) {
                    case 'deactivate':
                        $this->productService->adminToggleStatus($productIds, false);
                        break;
                    case 'activate':
                        $this->productService->adminToggleStatus($productIds,  true);
                        break;
                    default:
                        // Неизвестное действие
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

        // Если метод запроса не POST, перенаправляем обратно
        header('Location: /admin/products');
        exit;
    }
}