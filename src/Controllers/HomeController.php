<?php

namespace Controllers;



use Core\View;
use Core\Services\Product\ProductRepository;
use Core\Services\Product\ProductService;
use PDOException;

class HomeController {

	private static ProductService $productService;


	private static function initialize()
	{
		if (!isset(self::$productService))
		{
			$pdo = require_once __DIR__ . "/../config/database.php";
			$repository = new ProductRepository($pdo);
			self::$productService = new ProductService($repository);
		}
	}
	public static function index() {

		self::initialize();

		$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		define("ITEMS_PER_PAGE", 10);

		try 
		{

			$products = self::$productService->getPaginatedProducts($currentPage, ITEMS_PER_PAGE);
			$totalPages = self::$productService->getTotalPages(ITEMS_PER_PAGE);
			
			$content = View::make
			(
				__DIR__ . "/../Views/home/catalog.php",
				[
					'products' => $products,
					'totalPages' => $totalPages,
					'currentPage' => $currentPage,
						]
			);

			echo View::make
			(
				__DIR__ . '/../Views/layouts/main_template.php',
				[
							'content' => $content
						]
			);

		} 
		catch(PDOException $e)
		{
            error_log("Database error: " . $e->getMessage());
    		echo "Произошла ошибка при загрузке товаров.";
        }

	}
}