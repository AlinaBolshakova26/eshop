<?php
namespace Controllers\Admin;

use Core\Services\Admin\AdminService;
use Core\Services\Admin\AdminRepository;
use Core\Database\MySQLDatabase;

class AdminController
{
    private static AdminService $adminService;

    private static function initialize(): void
    {
        if (!isset(self::$adminService)) {
            $database = new MySQLDatabase();
            $pdo = $database->getConnection();

            $repository = new AdminRepository($pdo);
            self::$adminService = new AdminService($repository);
        }
    }

    public static function login(): void
    {
        require __DIR__ . '/../../Views/admin/auth/login.php';
    }

    public static function authenticate(): void
    {
        self::initialize();

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            try
            {
                $email = $_POST['email'];
                $password = $_POST['password'];

                if (self::$adminService->authenticate($email, $password))
                {
                    header('Location: /admin');
                    exit;
                }
                else
                {
                    header('Location: /admin/login?error=1');
                    exit;
                }
            }
            catch (\Exception $e)
            {
                error_log("Authentication error: " . $e->getMessage());
                header('Location: /admin/login?error=1');
                exit;
            }
        }
    }

    public static function index(): void
    {
        self::initialize();

        if (!isset($_SESSION['admin'])) {
            header('Location: /admin/login');
            exit;
        }

        require __DIR__ . '/../../Views/admin/products/index.php';
    }
}