<?php

namespace Controllers;

use Core\View;
use Core\Services\ProductService;
use Core\Services\TagService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\TagRepository;
use Core\Services\TransliterateService;

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

    public function index(?int $id = null, ?string $query = null): void
    {

        if (isset($_GET['searchInput'])) 
        {
            $searchInput = trim(strip_tags($_GET['searchInput']));
            if ($searchInput !== '') 
            {
                header('Location: /search/' . urlencode($searchInput));
                exit;
            } 
            else 
            {
                header('Location: /');
                exit;
            }
        }

        $selectedTagId = $id;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        define("ITEMS_PER_PAGE", 9);

        $searchQuery = '';

        if ($query)
        {
            $searchQuery = '%' . TransliterateService::transliterate(urldecode($query)) . '%';
        }
        
        try
        {
            $tags = $this->tagService->getAllTags();
            if ($searchQuery)
            {
                $searchResults = $this->productService->searchProducts($currentPage, ITEMS_PER_PAGE, $tags, $searchQuery);
                $products = $searchResults['items'];
                $totalPages = ceil($searchResults['total'] / ITEMS_PER_PAGE);
                $originalQuery = $query;
            }
            else
            {
                $products = $this->productService->getPaginatedProducts($currentPage, ITEMS_PER_PAGE, '', $selectedTagId);
                $totalPages = $this->productService->getTotalPages(ITEMS_PER_PAGE, $selectedTagId, query: $searchQuery);
            }

            if (empty($products)) 
            {
                throw new \Exception("No products found");
            }

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
                'searchQuery' => $searchQuery,
                'originalQuery' => $originalQuery ?? '',
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
        catch (\Exception $e) 
        {
            $content = View::make(__DIR__ . "/../Views/home/catalog.php", [
                'products' => [],
                'tags' => $tags,
                'selectedTagId' => $selectedTagId,
                'selectedTagName' => '',
                'error' => 'Товары не найдены',
                'totalPages' => 0,
                'currentPage' => 1,
                'searchQuery' => $searchQuery,
            ]);
         
            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                'content' => $content
            ]);
        }

    }

}