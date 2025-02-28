<?php

namespace Controllers\Admin;

use Core\Repositories\AdminRepository;
use Core\Services\AdminService;
use Core\View;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Core\Services\TagService;
use Core\Repositories\TagRepository;
use Core\Services\ImageService;
use Core\Repositories\ImageRepository;

class ProductCreateController
{
	
	private ProductService $productService;
	private TagService $tagService;
	private AdminService $adminService;
	private ImageService $imageService;

	public function __construct()
	{

		$database = new MySQLDatabase();
		$pdo = $database->getConnection();

		$productRepository = new ProductRepository($pdo);
        $ratingRepository = new RatingRepository($pdo);
		$imageRepository = new ImageRepository($pdo);

        $this->adminService = new AdminService(new AdminRepository($pdo));
        $this->productService = new ProductService
		(
            $productRepository,
            $ratingRepository
        );
        $this->tagService = new TagService(new TagRepository($pdo));
        $this->imageService = new ImageService($pdo);

	}

	public function create(): void
	{
		if (!$this->adminService->isAdminLoggedIn())
		{
			header('Location: /admin/login');
			exit;
		}

		$tags = $this->tagService->getAllTags();

		$content = View::make
		(__DIR__ . '/../../Views/admin/products/add_product.php',
	[
				'tags' => $tags,
			]
		);
		echo View::make
		(__DIR__ . '/../../Views/layouts/admin_layout.php',
	[
				'content' => $content,
			]
		);
	}

	public function store()
	{

		if (!$this->adminService->isAdminLoggedIn())
		{
			header('Location: /admin/login');
			exit;
		}

		$name = $_POST['name'];
		$description = $_POST['description'];
		$descShort = $_POST['desc_short'];
		$price = $_POST['price'];
		$mainImage = $_FILES['main_image'] ?? null;
		$additionalImages = $_FILES['additional_images'] ?? [];
		$tags = $_POST['tags'] ?? [];

		$productId = $this->productService->createProduct
		(
	  [
				'name' => $name,
				'description' => $description,
				'desc_short' => $descShort,
				'price' => $price,
			]
		);

		if (!empty($mainImage))
		{
			$this->imageService->saveImage($productId, $mainImage, true);
		}

		if (!empty($additionalImages)) 
		{
			foreach ($additionalImages['tmp_name'] as $key => $tmpName) 
			{
				$file = 
				[
					'tmp_name' => $tmpName,
					'name' => $additionalImages['name'][$key],
					'type' => $additionalImages['type'][$key],
					'error' => $additionalImages['error'][$key],
					'size' => $additionalImages['size'][$key]
				];

				$this->imageService->saveImage($productId, $file);
			}
		}

		if (!empty($tags)) 
		{
			$this->tagService->addTagsToProduct($productId, $tags);
		}

		header('Location: /admin/products');
		exit;

	}
	
}