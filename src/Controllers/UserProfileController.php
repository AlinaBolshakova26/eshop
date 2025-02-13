<?php

namespace Controllers;

use Core\View;
use Core\Services\UserService;
use Core\Database\MySQLDatabase;
use Core\Repositories\UserRepository;

class UserProfileController
{
    private UserService $userService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->userService = new UserService(new UserRepository($pdo));
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

            if (!$user) {
                throw new \Exception("Пользователь не найден");
            }

            $content = View::make(__DIR__ . "/../Views/user/profile.php", [
                'user' => $user,
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

        try {
            $result = $this->userService->updateUser($userId, $data);

            $message = $result ? "Профиль успешно обновлён." : "Ошибка обновления профиля.";
            $user = $this->userService->getUserById($userId);

            $content = View::make(__DIR__ . "/../Views/user/profile.php", [
                'user'    => $user,
                'message' => $message,
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
                'error' => $e->getMessage(),
            ]);

            echo View::make(__DIR__ . "/../Views/layouts/main_template.php", [
                'content' => $content,
            ]);
        }
    }
}