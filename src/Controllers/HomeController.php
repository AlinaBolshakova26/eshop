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

    public function index(): void
	{
		if (isset($_GET['tags']) && $_GET['tags'] === '') {
			header('Location: /');
			exit;
		}
		$selectedTagIds = isset($_GET['tags']) ? explode(',', $_GET['tags']) : [];
		$currentPage = max(1, (int)($_GET['page'] ?? 1));

		define("ITEMS_PER_PAGE", 9);

		$searchQuery = '';
		$searchValue = isset($_GET['searchInput']) ? (string)$_GET['searchInput'] : null;
		$minPrice = isset($_GET['minPrice']) ? (int)$_GET['minPrice'] : null;
		$maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : null;

		$priceError = null;
		if ($minPrice !== null || $maxPrice !== null) {
			if (!is_numeric($minPrice) && $minPrice !== null) {
				$priceError = "Минимальная цена должна быть числом.";
			} elseif (!is_numeric($maxPrice) && $maxPrice !== null) {
				$priceError = "Максимальная цена должна быть числом.";
			}
			elseif (($minPrice !== null && $minPrice < 0) || ($maxPrice !== null && $maxPrice < 0)) {
				$priceError = "Цены не могут быть отрицательными.";
			}
			elseif ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
				$priceError = "Минимальная цена не может быть больше максимальной.";
			}
		}

		try {
			$tags = $this->tagService->getAllTags(true);

			if ($searchValue) 
			{
				$searchQuery = TransliterateService::transliterate(urldecode($searchValue));

				$tagIdsLikeQuery = $this->tagService->getIdsLikeQuery($tags, $searchQuery); 
				$productIdsByTagIds = $this->productService->getIdsByTagIds($tagIdsLikeQuery); 

				$searchResults = $this->productService->searchProducts($currentPage, ITEMS_PER_PAGE, $productIdsByTagIds, $searchQuery, true, $minPrice, $maxPrice);

				$products = $searchResults['products'];
				
                $totalPages = ceil($searchResults['totalProducts'] / ITEMS_PER_PAGE);
			}
			else 
			{
				$products = $this->productService->getPaginatedProducts(
					$currentPage,
					ITEMS_PER_PAGE,
					$selectedTagIds,
					$minPrice,
					$maxPrice
				);
	
				$totalPages = $this->productService->getTotalPages(
					ITEMS_PER_PAGE,
					$selectedTagIds,
					$searchQuery,
					$minPrice,
					$maxPrice
				);

			}

			if ($priceError) {
				$minPrice = null;
				$maxPrice = null;
			}

			$selectedTagNames = [];
			foreach ($tags as $tag) {
				if (in_array($tag->toListDTO()->id, $selectedTagIds)) {
					$selectedTagNames[] = $tag->toListDTO()->name;
				}
			}

			$selectedTagName = !empty($selectedTagNames) ? implode(', ', $selectedTagNames) : null;

			if (empty($products)) {
				throw new \Exception("No products found");
			}

			$content = View::make(__DIR__ . "/../Views/home/catalog.php", [
				'products' => $products,
				'tags' => $tags,
				'selectedTagIds' => $selectedTagIds,
				'selectedTagName' => $selectedTagName,
				'totalPages' => $totalPages,
				'currentPage' => $currentPage,
				'searchQuery' => $searchQuery,
				'searchValue' => $searchValue,  
				'minPrice' => $minPrice,
				'maxPrice' => $maxPrice,
				'priceError' => $priceError,
			]);

			echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
				'content' => $content,
				'searchQuery' => $searchQuery,
				'searchValue' => $searchValue,  
			]);
		} catch (\PDOException $e) {
			error_log("Database error: " . $e->getMessage());
			echo "Произошла ошибка при загрузке товаров.";
		} catch (\Exception $e) {
			$content = View::make(__DIR__ . "/../Views/home/catalog.php", [
				'products' => [],
				'tags' => $tags,
				'selectedTagIds' => [],
				'selectedTagName' => '',
				'error' => 'Товары не найдены',
				'totalPages' => 0,
				'currentPage' => 1,
				'searchQuery' => $searchQuery,
				'searchValue' => $searchValue,  
				'minPrice' => $minPrice,
				'maxPrice' => $maxPrice,
				'priceError' => $priceError,
			]);

			echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
				'content' => $content,
				'searchQuery' => $searchQuery,
				'searchValue' => $searchValue,  
			]);
		}
	}
    
}