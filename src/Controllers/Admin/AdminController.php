<?php
namespace Controllers\Admin;

use Core\Services\Admin\AdminService;
use Core\Services\Admin\AdminRepository;
use Core\Database\MySQLDatabase;

class AdminController
{
    private AdminService $adminService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();
        $repository = new AdminRepository($pdo);
        $this->adminService = new AdminService($repository);
    }

    public function login(): void
    {
        require __DIR__ . '/../../Views/admin/auth/login.php';
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            try
            {
                $email = $_POST['email'];
                $password = $_POST['password'];

                if ($this->adminService->authenticate($email, $password))
                {
                    header('Location: /admin/products');
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

    public function logout(): void
    {
        $this->adminService->logout();
        header('Location: /admin/login');
        exit;
    }
}
