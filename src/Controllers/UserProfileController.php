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

    public function profile(?int $userId = null): void
    {
        if (!$userId)
        {
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId)
        {
            header("Location: /user/login");
            exit;
        }

        try
        {
            $user = $this->userService->getUserById($userId);
            $orders = $this->orderService->getOrdersByUserId($userId);
            $avatars = $this->userService->getAvatars();

            if (!$user) {
                throw new \Exception("Пользователь не найден");
            }

            $content = View::make(__DIR__ . "/../Views/user/profile.php", [
                'user' => $user,
                'avatars' => $avatars,
                'orders' => $orders
            ]);

            echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
                'content' => $content,
            ]);
        }
        catch (\PDOException $e)
        {
            error_log("Database error in profile: " . $e->getMessage());
            echo "Произошла ошибка при загрузке данных пользователя.";
        }
        catch (\Exception $e)
        {
            $content = View::make(__DIR__ . "/../Views/user/profile.php", [
                'user'  => null,
                'error' => $e->getMessage(),
                'orders' => []
            ]);

            echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
                'content' => $content,
            ]);
        }
    }

    public function update(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header("Location: /user/login");
            exit;
        }

        $data = $_POST;

        if (!isset($data['avatar']) || empty($data['avatar']))
        {
            $user = $this->userService->getUserById($userId);
            $data['avatar'] = $user['avatar'] ?? 'default.jpg';
        }
        else
        {
            $avatarDirectory = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatars/';
            if (!file_exists($avatarDirectory . $data['avatar']))
            {
                $data['avatar'] = 'default.jpg';
            }
        }

        try {
            $result = $this->userService->updateUser($userId, $data);

            $message = $result ? "Профиль успешно обновлён." : "Ошибка обновления профиля.";
            $user = $this->userService->getUserById($userId);
            $orders = $this->orderService->getOrdersByUserId($userId);
            $avatars = $this->userService->getAvatars();

            $content = View::make(__DIR__ . "/../Views/user/profile.php", [
                'user'    => $user,
                'message' => $message,
                'orders'  => $orders,
                'avatars' => $avatars
            ]);

            echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
                'content' => $content,
            ]);
        }
        catch (\PDOException $e) {
            error_log("Database error in update: " . $e->getMessage());
            echo "Произошла ошибка при обновлении профиля.";
        }
        catch (\Exception $e) {
            $content = View::make(__DIR__ . "/../Views/user/profile.php", [
                'user'  => $this->userService->getUserById($userId),
                'orders' => $this->orderService->getOrdersByUserId($userId),
                'error' => $e->getMessage(),
            ]);

            echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
                'content' => $content,
            ]);
        }
    }
}