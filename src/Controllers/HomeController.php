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
    private static ProductService $productService;
    private static ?TagService $tagService = null;

    private static function initialize(): void
    {
        if (!isset(self::$productService))
        {
            $database = new MySQLDatabase();
            $pdo = $database->getConnection();

            $repository = new ProductRepository($pdo);
            self::$productService = new ProductService($repository);

            if (self::$tagService === null)
            {
                self::$tagService = new TagService((new TagRepository($pdo)));
            }
        }
    }


    public static function index(?int $id = null): void
    {
        self::initialize();

        define("ITEMS_PER_PAGE", 9);
        $selectedTagId = $id;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));

        try {
            $tags = self::$tagService->getAllTags();
            $products = self::$productService->getPaginatedProducts($currentPage, ITEMS_PER_PAGE, $selectedTagId);
            $totalPages = self::$productService->getTotalPages(ITEMS_PER_PAGE, $selectedTagId);

            $selectedTagName = null;
            if ($selectedTagId !== null) {
                foreach ($tags as $tag) {
                    if ($tag->toListDTO()->id === $selectedTagId) {
                        $selectedTagName = $tag->toListDTO()->name;
                        break;
                    }
                }
            }

            $content = View::make(
                __DIR__ . "/../Views/home/catalog.php",
                [
                    'products' => $products,
                    'tags' => $tags,
                    'selectedTagId' => $selectedTagId,
                    'selectedTagName' => $selectedTagName,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage,
                ]
            );

            echo View::make(
                __DIR__ . '/../Views/layouts/main_template.php',
                [
                    'content' => $content,
                ]
            );
        }
        catch (\PDOException $e)
        {
            error_log("Database error: " . $e->getMessage());
            echo "Произошла ошибка при загрузке товаров.";
        }
    }
}