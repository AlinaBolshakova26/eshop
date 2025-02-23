<?php
namespace Controllers;

use Core\Repositories\RatingRepository;
use Core\Services\RatingService;

class RatingController
{
    private RatingRepository $ratingRepository;
    private RatingService $ratingService;

    public function __construct(
        RatingRepository $ratingRepository, RatingService $ratingService
    )
    {
        $this->ratingRepository= $ratingRepository;
        $this->ratingService = $ratingService;
    }

    public function create()
    {
        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$userId || $this->ratingRepository->hasUserRated($userId, $data['product_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Вы уже оценивали этот товар']);
            exit;
        }

        if (!isset($_SESSION['user_id']))
        {
            http_response_code(401);
            echo json_encode(['error' => 'Требуется авторизация']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $productId = $data['product_id'] ?? null;
        $rating = $data['rating'] ?? null;

        if (!$productId || !$rating || $rating < 1 || $rating > 5)
        {
            http_response_code(400);
            echo json_encode(['error' => 'Некорректные данные']);
            return;
        }

        try
        {
            $this->ratingRepository->createRating(
                    $_SESSION['user_id'],
                    $data['product_id'],
                    $data['rating']
                );

            echo json_encode(['success' => true]);
        }
        catch (Exception $e)
        {
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
        }
    }
}