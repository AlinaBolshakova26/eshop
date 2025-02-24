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

    if (!isset($_SESSION['user_id']))
    {
        http_response_code(401);
        echo json_encode(['error' => 'Требуется авторизация']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    $productId = $data['product_id'] ?? null;
    $rating = $data['rating'] ?? null;
    $comment = $data['comment'] ?? '';

    if (!$productId || !$rating || $rating < 1 || $rating > 5)
    {
        http_response_code(400);
        echo json_encode(['error' => 'Некорректные данные']);
        return;
    }

    if ($this->ratingRepository->hasUserRated($userId, $productId))
    {
        http_response_code(403);
        echo json_encode(['error' => 'Вы уже оценивали этот товар']);
        return;
    }

    try
    {
        $this->ratingRepository->createRating(
            $userId,
            $productId,
            $rating,
            $comment
        );

        echo json_encode(['success' => true]);
    }
    catch (\Exception $e)
    {
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
    }
}


}