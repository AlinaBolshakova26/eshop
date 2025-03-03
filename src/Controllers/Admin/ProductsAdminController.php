<?php

namespace Controllers\Admin;

use Core\Services\AdminService;
use Core\Services\ProductService;
use Core\Database\MySQLDatabase;
use Core\Repositories\AdminRepository;
use Core\Repositories\ProductRepository;
use Core\Services\TransliterateService;
use Core\Services\TagService;
use Core\Repositories\TagRepository;
use Core\Repositories\RatingRepository;
use Controllers\Admin\AdminBaseController;

class ProductsAdminController extends AdminBaseController
{

    private AdminService $adminService;
    private ProductService $productService;

    private TagService $tagService;


    public function __construct()
    {

        parent::__construct();

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $productRepository = new ProductRepository($pdo);
        $ratingRepository = new RatingRepository($pdo);
        $tagRepository = new TagRepository($pdo);

        $this->adminService = new AdminService(new AdminRepository($pdo));
        $this->productService = new ProductService
        (
            $productRepository,
            $ratingRepository
        );
        $this->tagService = new TagService($tagRepository);

    }

    public function index(?string $query = null): void
    {

        $searchQuery = '';
        $searchValue = $query ?? $_GET['searchInput'] ?? null;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));

        try 
        {
            if ($searchValue)
            {
                $tags = $this->tagService->getAllTags();

                $searchQuery = TransliterateService::transliterate($searchValue);

                $tagIdsLikeQuery = $this->tagService->getIdsLikeQuery($tags, $searchQuery); 
        $productIdsByTagIds = $this->productService->getIdsByTagIds($tagIdsLikeQuery);

                $searchResults = $this->productService->searchProducts($currentPage, ITEMS_PER_PAGE_ADMIN, $productIdsByTagIds, $searchQuery, false);

        $products = $searchResults['products'];
                $totalPages = ceil($searchResults['totalProducts'] / ITEMS_PER_PAGE_ADMIN);
            }
            else 
            {
                $products = $this->productService->adminGetPaginatedProducts($currentPage, ITEMS_PER_PAGE_ADMIN);
                $totalPages = $this->productService->getTotalPages(ITEMS_PER_PAGE_ADMIN);
            }

            $this->render
            (
                'admin/products/index', 
                [
                    'products' => $products,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage,
                    'searchQuery' => $searchQuery,
                    'searchValue' => $searchValue,
                    // 'error' => View::make('admin/error_block')
                ]
            );
        }
        catch (\PDOException $e)
        {
            echo "Произошла ошибка при загрузке товаров.";
        }

    }

    public function process(): void
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            if (isset($_POST['action']) && $_POST['action'] === 'search')
            {
                $searchInput = trim(strip_tags($_POST['searchInput'] ?? ''));
                if ($searchInput !== '') 
                {
                    $this->redirect('/admin/products?searchInput=' . urlencode($searchInput));
                    
                }
                $this->redirect('/admin/products');
            }

            $selectedProducts = $_POST['selected_products'] ?? [];
            $action = $_POST['action'] ?? '';

            if (empty($selectedProducts)) 
            {
                $this->redirect('/admin/products?error=no_products_selected');
            }

            try 
            {
                $productIds = array_map('intval', $selectedProducts);


                switch ($action) 
                {
                    case 'deactivate':
                        $this->productService->adminToggleStatus($productIds, false);
                        break;
                    case 'activate':
                        $this->productService->adminToggleStatus($productIds, true);
                        break;
                    default:
                        $this->redirect('/admin/products?error=invalid_action');
                }

                $this->redirect('/admin/products?success=1');
            }
            catch (\PDOException $e)
            {
                error_log("Database error: " . $e->getMessage());
                $this->redirect('/admin/products?error=database_error');
            }
        }

        $this->redirect('/admin/products');
    }
    
}
