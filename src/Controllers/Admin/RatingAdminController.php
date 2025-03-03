<?php
namespace Controllers\Admin;

use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Core\Services\ProductService;
use Core\Services\RatingService;
use Controllers\Admin\AdminBaseController;

class RatingAdminController extends AdminBaseController
{
    
    private RatingService $ratingService;

    public function __construct()
    {
        parent::__construct();

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->ratingService = new RatingService(new RatingRepository($pdo));
    }


    public function index(): void
    {
        try
        {
            $currentPage = max(1, (int)($_GET['page'] ?? 1));

            $ratings = $this->ratingService->getPaginatedRatings($currentPage, BY_RATING_OR_TAG_ITEMS_PER_PAGE_ADMIN);

            $totalPages = $this->ratingService->getTotalPages(BY_RATING_OR_TAG_ITEMS_PER_PAGE_ADMIN);

            $this->render
            (
                'admin/ratings/index', 
            [
                    'ratings' => $ratings,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage
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

            $this->render
            (
                'admin/ratings/detail', 
            [
                    'rating' => $rating
                ]
            );
        }
        catch (\Exception $e)
        {
            http_response_code(404);
            echo "Оценка не найдена";
        }

    }
    
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            $this->redirect(url('admin.ratings-index'));
            exit;
        }

        $ratingIds = $_POST['rating_ids'] ?? [];
        if (!empty($ratingIds) && is_array($ratingIds)) 
        {
            $success = $this->ratingService->deleteRatings($ratingIds);
            if ($success) 
            {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Рейтинги успешно удалены'];
            } 
            else 
            {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ошибка при удалении рейтингов'];
            }
        } 
        else 
        {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Не выбраны рейтинги для удаления'];
        }

        $this->redirect(url('admin.ratings-index'));
    }
}
