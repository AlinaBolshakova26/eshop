<?php

namespace Core\Services;

use Core\Repositories\UserRepository;
use Models\User\User;

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(int $userId)
    {
        return $this->userRepository->findById($userId);
    }

    public function updateUser(int $userId, array $data): bool
    {

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            throw new \InvalidArgumentException("Неверный формат email");
        }

        if (isset($data['password']) && !empty($data['password']))
        {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        else
        {
            unset($data['password']);
        }

        return $this->userRepository->update($userId, $data);

    }

    public function login(string $email, string $password): ?array
    {

        $user = $this->userRepository->findByEmail($email);

        if ($user && isset($user['password']) && password_verify($password, $user['password']))
        {
            return $user;
        }

        return null;

    }

    public function register(array $data): bool
    {

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            throw new \InvalidArgumentException("Неверный формат email");
        }

        $existingUser = $this->userRepository->findByEmail($data['email']);

        if ($existingUser !== null)
        {
            throw new \Exception("Пользователь с таким email уже существует");
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        return $this->userRepository->create($data);
        
    }

    public function getAvatars(): array
    {
        
        $avatarPath = "/assets/images/avatars/";
        $fullPath = __DIR__ . "/../../../public" . $avatarPath;

        if (!is_dir($fullPath))
        {
            return [];
        }

        $avatars = scandir($fullPath);

        return array_filter
        ($avatars, function($avatar)
            {
                return $avatar !== "." && $avatar !== "..";
            }
        );

    }
    
}