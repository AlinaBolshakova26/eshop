<?php
declare(strict_types=1);

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
    private ?string $password = null;
    private string $role;
    private string $createdAt;
    private string $updatedAt;
    private ?string $address;

    private function __construct() {}

    public static function fromDatabase(array $row): self
    {
        $user = new self();

        $user->id        = (int)$row['id'];
        $user->name      = $row['name'];
        $user->phone     = $row['phone'];
        $user->email     = $row['email'];
        $user->password  = $row['password'] ?? null;
        $user->role      = $row['role'];
        $user->createdAt = $row['created_at'] ?? date('Y-m-d H:i:s');
        $user->updatedAt = $row['updated_at'] ?? date('Y-m-d H:i:s');

        return $user;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function updateProfile(string $name, string $phone, string $email, ?string $address): void
    {
        $this->name  = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->touch();
    }

    public function changeRole(string $role): void
    {
        if (!in_array($role, ['admin', 'customer'], true))
        {
            throw new \InvalidArgumentException("Недопустимая роль: $role");
        }

        $this->role = $role;
        $this->touch();
    }

    public function verifyPassword(string $password): bool
    {
        if ($this->password === null)
        {
            error_log("Пароль не установлен для пользователя с ID {$this->id}");
            return false;
        }

        $isValid = password_verify($password, $this->password);

        if ($isValid)
        {
            error_log("Пароль корректный для пользователя с ID {$this->id}");
        }
        else
        {
            error_log("Пароль некорректный для пользователя с ID {$this->id}");
        }

        return $isValid;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = date('Y-m-d H:i:s');
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
