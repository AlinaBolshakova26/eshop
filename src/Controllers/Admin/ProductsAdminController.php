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
}