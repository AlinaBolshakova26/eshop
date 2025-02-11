<?php

namespace Models;

final class UserListDTO
{

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $email,
        public readonly string $role
    ) {}

}

class User
{

    private int $id;
    private string $name;
    private string $phone;
    private string $email;
    private ?string $password;
    private string $role;
    private string $created_at;
    private string $updated_at;

    public static function fromDatabase(array $row): self
    {

        $user = new self();

        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->phone = $row['phone'];
        $user->email = $row['email'];
        $user->password = $row['password'] ?? null;
        $user->role = $row['role'];
        $user->created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
        $user->updated_at = $row['updated_at'] ?? date('Y-m-d H:i:s');

        return $user;

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function updateProfile(string $name, string $phone, string $email): void
    {

        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->updated_at = date('Y-m-d H:i:s');

    }

    public function changeRole(string $role): void
    {

        if (!in_array($role, ['admin', 'customer']))
        {
            throw new \InvalidArgumentException("Invalid role");
        }

        $this->role = $role;
        $this->updated_at = date('Y-m-d H:i:s');

    }

    public function verifyPassword(string $password): bool
    {

        if ($this->password === null)
        {
            error_log("Stored password is NULL");

            return false;
        }

        if (password_verify($password, $this->password))
        {
            error_log("Password is correct!");

            return true;
        }
        else
        {
            error_log("Password is incorrect!");

            return false;
        }

    }

    public function setPassword(string $password): void
    {

        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->updated_at = date('Y-m-d H:i:s');

    }

    public function toListDTO(): UserListDTO
    {

        return new UserListDTO(
            $this->id,
            $this->name,
            $this->phone,
            $this->email,
            $this->role
        );

    }
    
}
