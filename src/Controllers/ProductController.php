<?php

namespace Controllers;

use Core\Services\Product\ProductRepository;
use Core\Services\Product\ProductService;
use Core\View;
use PDOException;

class ProductController
{

	private ProductService $productService;

	private function initialize()
	{
		if (!isset($this->productService))
		{
			$pdo = require_once __DIR__ . "/../config/database.php";
			$repository = new ProductRepository($pdo);
			$this->productService = new ProductService($repository);
		}
	}

	public function show($id) 
	{

		$this->initialize();
		
		try {

			$product = $this->productService->getProductByid($id);

			if (!$product) {
				http_response_code(404);
				echo '404 Not Found';
				return;
			}

			$content = View::make(__DIR__ . '/../Views/product/detail.php', [
				'product' => $product,
				// 'productImages' => $productImages
			]);

			echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
				'content' => $content
			]);
		}
		catch(PDOException $e)
		{
			error_log("Database error: " . $e->getMessage());
    		echo "Произошла ошибка при загрузке товара.";
		}
	}

}