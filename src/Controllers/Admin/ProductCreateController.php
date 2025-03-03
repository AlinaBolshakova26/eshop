<?php

namespace Controllers\Admin;

use Core\Repositories\AdminRepository;
use Core\Services\AdminService;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Core\Services\TagService;
use Core\Repositories\TagRepository;
use Core\Services\ImageService;
use Controllers\Admin\AdminBaseController;
use Core\Repositories\ImageRepository;

class ProductCreateController extends AdminBaseController
{
  
  private ProductService $productService;
  private TagService $tagService;
  private AdminService $adminService;
  private ImageService $imageService;

  public function __construct()
  {
    parent::__construct();

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
        $this->imageService = new ImageService($imageRepository);

  }

  public function create(): void
  {

    $tags = $this->tagService->getAllTags();

    $this->render
    (
      'admin/products/add_product',
      [
        'tags' => $tags,
      ]
    );
  }

  public function store()
  {

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

    $this->redirect(url('admin.products'));
  }
  
}
