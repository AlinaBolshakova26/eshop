<?php

namespace Controllers;

use Core\View;
use Core\Services\UserService;
use Core\Services\OrderService;
use Core\Database\MySQLDatabase;
use Core\Repositories\UserRepository;
use Core\Repositories\OrderRepository;

class UserProfileController
{
    private UserService $userService;
    private OrderService $orderService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->userService = new UserService(new UserRepository($pdo));
        $this->orderService = new OrderService(new OrderRepository($pdo));
    }

    public function profile(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId)
        {
            header("Location: /user/login");
            exit;
        }

        $user = $this->userService->getUserById($userId);
        $orders = $this->orderService->getOrdersByUserId($userId);
        $avatars = $this->userService->getAvatars();

        echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
            'content' => View::make(__DIR__ . "/../Views/user/profile.php", [
                'user' => $user,
                'avatars' => $avatars,
                'orders' => $orders
            ]),
        ]);
    }

    public function update(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId)
        {
            header("Location: /user/login");
            exit;
        }

        $data = $_POST;
        $user = $this->userService->getUserById($userId);

        $data['avatar'] = !empty($data['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatars/' . $data['avatar'])
            ? $data['avatar']
            : ($user['avatar'] ?? 'default.jpg');

        $result = $this->userService->updateUser($userId, $data);

        echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
            'content' => View::make(__DIR__ . "/../Views/user/profile.php", [
                'user' => $this->userService->getUserById($userId),
                'message' => $result ? "Профиль успешно обновлён." : "Ошибка обновления профиля.",
                'orders' => $this->orderService->getOrdersByUserId($userId),
                'avatars' => $this->userService->getAvatars(),
            ]),
        ]);
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