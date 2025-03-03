<?php

namespace Controllers;

use Controllers\BaseController;
use Core\Services\UserService;
use Core\Database\MySQLDatabase;
use Core\Repositories\UserRepository;
use Requests\UserRegistrationRequest;

class UserController extends BaseController
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
        $this->render('user/auth/login');
    }

    public function authenticate(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userService->login($email, $password);

        if ($user)
        {
            $_SESSION['user_id'] = $user['id'];
            $this->redirect(url('user.profile'));
        }
        else
        {
            $error = "Неверный email или пароль.";
            $this->render
            (
                'user/auth/login',
                [
                    'error' => $error
                ]
            );
        }
    }

    public function logout(): void
    {

        session_destroy();
        $this->redirect(url('user.login'));

    }

    public function register(): void
    {
        $this->render('user/auth/register');
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
            $this->render
            (
                'user/auth/register',
                [
                    'error' => $e->getMessage()
                ]
            );
            return;
        }

        try
        {
            $userData = $registrationRequest->getData();
            $result = $this->userService->register($userData);

            if ($result)
            {
                $this->redirect(url('user.login'));
            }
            else
            {
                $error = "Ошибка регистрации. Попробуйте еще раз.";
                $this->render
                (
                    'user/auth/register',
                    [
                        'error' => $error
                    ]
                );
            }
        }
        catch (\Exception $e)
        {
            $error = "Ошибка регистрации: " . $e->getMessage();
            $this->render
            (
                'user/auth/register',
                [
                    'error' => $error
                ]
            );
        }

    }
    
}
