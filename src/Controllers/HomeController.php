<?php

namespace Controllers;

use Controllers\BaseController;
use Core\Services\ProductService;
use Core\Services\TagService;
use Core\Services\RatingService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\TagRepository;
use Core\Repositories\RatingRepository;
use Core\Services\TransliterateService;
use Core\Exceptions\AppException;
use Core\Exceptions\ValidationException;

class HomeController extends BaseController
{

    private ProductService $productService;
    private ?TagService $tagService = null;
    private RatingService $ratingService;

    public function __construct()
    {

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $productRepository = new ProductRepository($pdo);
        $ratingRepository = new RatingRepository($pdo);
        
        $this->tagService = new TagService(new TagRepository($pdo));
        $this->productService = new ProductService
        (
            $productRepository,
            $ratingRepository
        );
        $this->ratingService = new RatingService($ratingRepository);

    }

    public function index(): void
  {

    if (isset($_GET['tags']) && $_GET['tags'] === '') 
        {
      $this->redirect(url('catalog-index'));
    }

    $selectedTagIds = isset($_GET['tags']) ? explode(',', $_GET['tags']) : [];
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    
        $searchQuery = '';
        $searchValue = isset($_GET['searchInput']) ? (string)$_GET['searchInput'] : null;
        $minPrice = isset($_GET['minPrice']) && $_GET['minPrice'] !== '' ? (int)$_GET['minPrice'] : null;
        $maxPrice = isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '' ? (int)$_GET['maxPrice'] : null;

        $priceError = null;

        if ($minPrice !== null || $maxPrice !== null)
        {
            if (!is_numeric($minPrice) && $minPrice !== null)
            {
                $priceError = "Минимальная цена должна быть числом.";
            } 
            elseif (!is_numeric($maxPrice) && $maxPrice !== null)
            {
                $priceError = "Максимальная цена должна быть числом.";
            }
            elseif (($minPrice !== null && $minPrice < 0) || ($maxPrice !== null && $maxPrice < 0))
            {
                $priceError = "Цены не могут быть отрицательными.";
            }
            elseif ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice)
            {
                $priceError = "Минимальная цена не может быть больше максимальной.";
            }
        }

    
        $tags = $this->tagService->getAllTags();

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
            $products = $this->productService->getPaginatedProducts
            (
                $currentPage,
                ITEMS_PER_PAGE,
                $selectedTagIds,
                $minPrice,
                $maxPrice
            );

            $totalPages = $this->productService->getTotalPages
            (
                ITEMS_PER_PAGE,
                $selectedTagIds,
                $searchQuery,
                $minPrice,
                $maxPrice
            );
        }

        if ($priceError)
        {
            $minPrice = null;
            $maxPrice = null;
        }

        $selectedTagNames = [];


        foreach ($tags as $tag) 
        {
            if (in_array($tag->toListDTO()->id, $selectedTagIds))
            {
                $selectedTagNames[] = $tag->toListDTO()->name;
            }
        }

        $selectedTagName = !empty($selectedTagNames) ? implode(', ', $selectedTagNames) : null;

        $productIds = array_map(fn($product) => $product->id, $products);
        $ratingsMap = $this->ratingService->getRatingsForProducts($productIds);

        $this->render
        (
            'home/catalog',
            [
                'products' => $products,
                'ratingsMap' => $ratingsMap,
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
            ],
            [
                'searchQuery' => $searchQuery,
                'searchValue' => $searchValue,
            ]
        );
        
    }

}
