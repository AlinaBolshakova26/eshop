<?php
namespace Core\Services\Admin;

use Models\User;
use Core\Services\Admin\AdminRepository;

class AdminService
{
    private AdminRepository $repository;

    public function __construct(AdminRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authenticate(string $email, string $password): bool
    {
        $user = $this->repository->findUserByEmail($email);

        if ($user && $user->verifyPassword($password) && $user->getRole() === 'admin')
        {
            session_start();
            $_SESSION['admin'] = $user->getId();
            return true;
        }

        return false;
    }

    public function isAdminLoggedIn(): bool
    {
        return isset($_SESSION['admin']);
    }
}