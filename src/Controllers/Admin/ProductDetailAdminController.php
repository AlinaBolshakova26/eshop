<?php

namespace Controllers\Admin;

use Core\Repositories\AdminRepository;
use Core\Repositories\TagRepository;
use Core\Services\AdminService;
use Core\Services\ImageService;
use Core\Services\TagService;
use Core\View;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Core\Repositories\ImageRepository;
use PDO;

class ProductDetailAdminController
{

    private ProductService $productService;
	private AdminService $adminService;
	private TagService $tagService;
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
        $this->imageService = new ImageService($pdo, $imageRepository);
    }

    public function edit(int $id): void
    {
		
		if (!$this->adminService->isAdminLoggedIn())
		{
			header('Location: /admin/login');
			exit;
		}

        $product = $this->productService->adminGetProductByid($id);
		$allTags = $this->tagService->getAllTags();
		$productTags = $this->tagService->getTagsByProductId($id);
                
        if (!$product)
        {
            header('Location: /admin/products');
            exit;
        }

        $content = View::make
		(__DIR__ . '/../../Views/admin/products/detail.php', 
	[
                'product' => $product,
				'allTags' => $allTags,
				'productTags' => $productTags,
            ]
        );

        echo View::make
		(__DIR__ . '/../../Views/layouts/admin_layout.php', 
	[
                'content' => $content,
            ]
        );

    }

	public function update(int $id)
	{

		if (!$this->adminService->isAdminLoggedIn())
		{
			header('Location: /admin/login');
			exit;
		}

		$selectedTagIds = $_POST['tags'] ?? [];
		$main_image = $_FILES['main_image'] ?? null;
		$additional_images = $_FILES['additional_images'] ?? [];
		$imagesToDelete = $_POST['images_to_delete'] ?? [];
		$this->productService->updateProduct
		($id,
			[
				'name' => $_POST['name'],
				'description' => $_POST['description'],
				'desc_short' => $_POST['desc_short'],
				'price' => $_POST['price'],
				'is_active' => $_POST['is_active'],
			]
		);

		$this->tagService->updateProductTags($id, $selectedTagIds);

		if (!empty($main_image))
		{
			$this->imageService->saveImage($id, $main_image, true);
		}

		if (!empty($additional_images)) 
		{
			foreach ($additional_images['tmp_name'] as $key => $tmpName) 
			{
				$file = 
				[
					'tmp_name' => $tmpName,
					'name' => $additional_images['name'][$key],
					'type' => $additional_images['type'][$key],
					'error' => $additional_images['error'][$key],
					'size' => $additional_images['size'][$key]
				];

				$this->imageService->saveImage($id, $file);
			}
		}

		if (!empty($imagesToDelete))
		{
			foreach ($imagesToDelete as $imageId)
			{
				$this->imageService->deleteImage($imageId);
			}
		}

		header('Location: /admin/products');
		exit;

	}
	
}