<?php

namespace Controllers;

use Core\View;
use Core\Services\UserService;
use Core\Database\MySQLDatabase;
use Core\Repositories\UserRepository;
use Requests\UserRegistrationRequest;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->userService = new UserService(new UserRepository($pdo));
    }

    public function index(): void
    {
        $content = View::make(__DIR__ . '/../Views/user/auth/login.php');
        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content,
        ]);
    }

    public function authenticate(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try
        {
            $user = $this->userService->login($email, $password);

            if ($user)
            {
                $_SESSION['user_id'] = $user['id'];
                header("Location: /user/profile");
                exit;
            }
            else
            {
                $error = "Неверный email или пароль.";
                $content = View::make(__DIR__ . '/../Views/user/auth/login.php', ['error' => $error]);
                echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                    'content' => $content,
                ]);
            }
        }
        catch (\Exception $e)
        {
            error_log("Ошибка при аутентификации: " . $e->getMessage());
            $error = "Произошла ошибка при попытке входа.";
            $content = View::make(__DIR__ . '/../Views/user/auth/login.php', ['error' => $error]);
            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                'content' => $content,
            ]);
        }
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /user/login");
        exit;
    }

    public function register(): void
    {
        $content = View::make(__DIR__ . '/../Views/user/auth/register.php');
        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content,
        ]);
    }

    public function store(): void
    {
        try
        {
            $registrationRequest = new UserRegistrationRequest($_POST);
            $registrationRequest->validate();
        }
        catch (\InvalidArgumentException $e)
        {
            $content = View::make(__DIR__ . '/../Views/user/auth/register.php', ['error' => $e->getMessage()]);
            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', ['content' => $content]);
            return;
        }

        try
        {
            $userData = $registrationRequest->getData();
            $result = $this->userService->register($userData);
            if ($result)
            {
                header("Location: /user/login");
                exit;
            }
            else
            {
                $error = "Ошибка регистрации. Попробуйте еще раз.";
                $content = View::make(__DIR__ . '/../Views/user/auth/register.php', ['error' => $error]);
                echo View::make(__DIR__ . '/../Views/layouts/main_template.php', ['content' => $content]);
            }
        }
        catch (\Exception $e)
        {
            error_log("Ошибка регистрации: " . $e->getMessage());
            $error = "Ошибка регистрации: " . $e->getMessage();
            $content = View::make(__DIR__ . '/../Views/user/auth/register.php', ['error' => $error]);
            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', ['content' => $content]);
        }
    }
}