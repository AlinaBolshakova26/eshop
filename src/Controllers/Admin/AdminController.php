<?php

namespace Controllers\Admin;

use Core\Services\AdminService;
use Core\Database\MySQLDatabase;
use Core\Repositories\AdminRepository;
use Controllers\Admin\AdminBaseController;
use Core\Session;

class AdminController extends AdminBaseController
{

    private AdminService $adminService;

    public function __construct()
    {

        parent::__construct();

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $repository = new AdminRepository($pdo);
        $this->adminService = new AdminService($repository);

    }

    public function login(): void
    {
        $this->renderWithoutLayout('admin/auth/login');
    }

    public function authenticate(): void
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->adminService->authenticate($email, $password))
            {
                $this->redirect(url('admin.products'));
            }

            $this->redirect(url('admin.login') . '?error=1');
        }

    }

    public function logout(): void
    {

        $this->adminService->logout();
        $this->redirect(url('admin.login'));

    }
    
}
