<?php
namespace Controllers\Admin;

use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Core\Services\ProductService;
use Core\Services\RatingService;
use Core\View;

class RatingAdminController
{
    
    private RatingService $ratingService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->ratingService = new RatingService(new RatingRepository($pdo));
    }


    public function index(): void
    {
        try
        {
            $currentPage = max(1, (int)($_GET['page'] ?? 1));
            define("ITEMS_PER_PAGE", 10);

            $ratings = $this->ratingService->getPaginatedRatings($currentPage, ITEMS_PER_PAGE);

            $totalPages = $this->ratingService->getTotalPages(ITEMS_PER_PAGE);

            $content = View::make
            (__DIR__ . '/../../Views/admin/ratings/index.php', 
        [
                    'ratings' => $ratings,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage
                ]
            );

            echo View::make
            (__DIR__ . '/../../Views/layouts/admin_layout.php', 
        [
                    'content' => $content,
                ]
            );
        }
        catch (\Exception $e)
        {
            echo "Ошибка: " . htmlspecialchars($e->getMessage());
        }

    }

    public function show(int $id): void
    {

        try
        {
            $rating = $this->ratingService->getRatingDetails($id);

            $content = View::make
            (__DIR__ . '/../../Views/admin/ratings/detail.php', 
        [
                    'rating' => $rating
                ]
            );

            echo View::make
            (__DIR__ . '/../../Views/layouts/admin_layout.php', 
        [
                   'content' => $content,
                ]
            );
        }
        catch (\Exception $e)
        {
            http_response_code(404);
            echo "Оценка не найдена";
        }

    }
    
}