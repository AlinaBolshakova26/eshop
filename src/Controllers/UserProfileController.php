<?php

namespace Controllers;

;
use Core\Services\UserService;
use Core\Services\OrderService;
use Core\Database\MySQLDatabase;
use Core\Repositories\UserRepository;
use Core\Repositories\OrderRepository;
use Core\Repositories\RatingRepository;
use Controllers\BaseController;

class UserProfileController extends BaseController
{
    
    private UserService $userService;
    private RatingRepository $ratingRepository;
    private OrderService $orderService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->userService = new UserService(new UserRepository($pdo));
        $this->orderService = new OrderService(new OrderRepository($pdo));
        $this->ratingRepository= new RatingRepository($pdo);
    }

    public function profile(): void
    {
        $this->checkLogin();
     
        $userId = $_SESSION['user_id'];

        $user = $this->userService->getUserById($userId);
        $orders = $this->orderService->getOrdersByUserId($userId);
        $avatars = $this->userService->getAvatars();

        if ($user)
        {
            $productIds = array_map(function ($order)
            {
                return $order['product_id'];
            },
            array_filter($orders, fn($o) => stripos($o['status'], 'Доставлен') !== false));

            $ratings = [];

            if (!empty($productIds))
            {
                foreach ($productIds as $pid)
                {
                    $ratings[$pid] = 
                    [
                        'value' => $this->ratingRepository->getRatingByUserAndProduct($user['id'], $pid),
                        'rated' => $this->ratingRepository->hasUserRated($user['id'], $pid),
                    ];
                }
            }

            $this->render
            (
                'user/profile',
                [
                    'user' => $user,
                    'avatars' => $avatars,
                    'orders' => $orders,
                    'ratings' => $ratings
                ]
            );
        }

    }

    public function update(): void
    {
        $this->checkLogin();
     
        $userId = $_SESSION['user_id'];

        $data = $_POST;
        $user = $this->userService->getUserById($userId);

        $data['avatar'] = !empty($data['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatars/' . $data['avatar'])
            ? $data['avatar']
            : ($user['avatar'] ?? 'default.jpg');

        $result = $this->userService->updateUser($userId, $data);

        $this->render
        (
            'user/profile',
            [
                'user' => $this->userService->getUserById($userId),
                'message' => $result ? "Профиль успешно обновлён." : "Ошибка обновления профиля.",
                'orders' => $this->orderService->getOrdersByUserId($userId),
                'avatars' => $this->userService->getAvatars(),
            ]
        );

    }

    public function updateAvatar(): void
    {
        
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId)
        {
            http_response_code(403);
            echo json_encode(["success" => false, "error" => "Пользователь не авторизован"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $avatar = $data['avatar'] ?? 'default.jpg';

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatars/' . $avatar))
        {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Файл не найден"]);
            exit;
        }

        $result = $this->userService->updateUser($userId, ['avatar' => $avatar]);

        echo json_encode(["success" => $result]);

    }
    
}
