<?php

namespace Core\Services\Admin;

use Models\User;
use Core\Services\Admin\AdminRepository;
use Core\Session;

class AdminService
{
    private AdminRepository $repository;

    public function __construct(AdminRepository $repository)
    {
        $this->repository = $repository;
    }

    public function authenticate(string $email, string $password): bool
    {
        $user = $this->repository->findUserByEmail($email);

        if ($user && $user->verifyPassword($password) && $user->getRole() === 'admin')
        {
            Session::start();
            Session::set('admin', $user->getId());
            return true;
        }
        return false;
    }

    public function isAdminLoggedIn(): bool
    {
        Session::start();
        return Session::has('admin');
    }

    public function logout(): void
    {
        Session::destroy();
    }
}
