<?php
namespace Controllers;

use Core\View;
use Core\Services\Product\ProductRepository;
use Core\Services\Product\ProductService;
use Core\Services\Product\TagService;
use Core\Services\Product\TagRepository;
use Core\Database\MySQLDatabase;

class HomeController
{
    private ProductService $productService;
    private ?TagService $tagService = null;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->productService = new ProductService(new ProductRepository($pdo));
        $this->tagService = new TagService(new TagRepository($pdo));
    }

    public function index(?int $id = null): void
    {
        $selectedTagId = $id;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        define("ITEMS_PER_PAGE", 9);

        try
        {
            $tags = $this->tagService->getAllTags();
            $products = $this->productService->getPaginatedProducts($currentPage, ITEMS_PER_PAGE, $selectedTagId);
            $totalPages = $this->productService->getTotalPages(ITEMS_PER_PAGE, $selectedTagId);

            $selectedTagName = null;
            foreach ($tags as $tag)
            {
                if ($tag->toListDTO()->id === $selectedTagId)
                {
                    $selectedTagName = $tag->toListDTO()->name;
                    break;
                }
            }

            $content = View::make(__DIR__ . "/../Views/home/catalog.php", [
                'products' => $products,
                'tags' => $tags,
                'selectedTagId' => $selectedTagId,
                'selectedTagName' => $selectedTagName,
                'totalPages' => $totalPages,
                'currentPage' => $currentPage,
            ]);

            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
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