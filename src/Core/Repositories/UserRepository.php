<?php
namespace Core\Repositories;

use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $userId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, email, phone, role, password, avatar, created_at, updated_at 
             FROM up_user 
             WHERE id = :id"
        );
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, email, phone, role, password, avatar, created_at, updated_at 
             FROM up_user 
             WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function update(int $userId, array $data): bool
    {
        $fields = [];
        $params = [];

        foreach ($data as $column => $value)
        {
            $fields[] = "`$column` = :$column";
            $params[":$column"] = $value;
        }

        if (empty($fields))
        {
            return false;
        }

        $params[':id'] = $userId;
        $sql = "UPDATE up_user SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO up_user (name, phone, email, password, role, avatar)
                VALUES (:name, :phone, :email, :password, :role, :avatar)";
        $stmt = $this->pdo->prepare($sql);
        $role = $data['role'] ?? 'customer';
        $avatar = $data['avatar'] ?? 'default.png';
        return $stmt->execute([
            ':name'  => $data['name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role'  => $role,
            ':avatar' => $avatar,
        ]);
    }
}