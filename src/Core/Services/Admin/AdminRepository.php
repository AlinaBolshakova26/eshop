<?php
namespace Core\Services\Admin;

use PDO;
use Models\User;

class AdminRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT id, name, phone, email, password, role 
            FROM up_user 
            WHERE email = :email
        ");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row)
        {
            return User::fromDatabase($row);
        }

        return null;
    }
}