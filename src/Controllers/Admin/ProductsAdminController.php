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
	private static AdminService $adminService;
	private static ProductService $productService;

	private static function initialize(): void
	{
		if (!isset(self::$productService) || !isset(self::$adminService))
		{
			$database = new MySQLDatabase();
			$pdo = $database->getConnection();

			if (!isset(self::$adminService))
			{
				$adminRepository = new AdminRepository($pdo);
				self::$adminService = new AdminService($adminRepository);
			}

			if (!isset(self::$productService)) {
				$productRepository = new ProductRepository($pdo);
				self::$productService = new ProductService($productRepository);
			}
		}

	}
	public static function index(): void
	{
		self::initialize();

		if (!self::$adminService->isAdminLoggedIn())
		{
			header('Location: /admin/login');
			exit;
		}

		$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		define("ITEMS_PER_PAGE", 30);

		try {
			$products = self::$productService->getPaginatedProducts($currentPage, ITEMS_PER_PAGE);
			$totalPages = self::$productService->getTotalPages(ITEMS_PER_PAGE);

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

	public static function process(): void
	{
		self::initialize();

		if (!self::$adminService->isAdminLoggedIn()) {
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
				switch ($action) {
					case 'delete':
						self::$productService->deleteProducts($selectedProducts);
						break;
					case 'activate':
						self::$productService->activateProducts($selectedProducts);
						break;
					default:
						// Неизвестное действие
						header('Location: /admin/products?error=invalid_action');
						exit;
				}

				// Перенаправляем обратно с сообщением об успехе
				header('Location: /admin/products?success=1');
				exit;
			} catch (\PDOException $e) {
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