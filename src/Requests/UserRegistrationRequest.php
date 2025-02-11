<?php
declare(strict_types=1);

namespace Requests;

class UserRegistrationRequest
{
    public string $name;
    public string $phone;
    public string $email;
    public string $password;
    public string $passwordConfirm;

    public function __construct(array $postData)
    {
        $this->name            = trim($postData['name'] ?? '');
        $this->phone           = trim($postData['phone'] ?? '');
        $this->email           = trim($postData['email'] ?? '');
        $this->password        = $postData['password'] ?? '';
        $this->passwordConfirm = $postData['password_confirm'] ?? '';
    }

    public function validate(): void
    {
        if (!$this->name || !$this->phone || !$this->email || !$this->password)
        {
            throw new \InvalidArgumentException("Все поля обязательны для заполнения.");
        }
        if ($this->password !== $this->passwordConfirm)
        {
            throw new \InvalidArgumentException("Пароли не совпадают.");
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
        {
            throw new \InvalidArgumentException("Неверный формат email.");
        }
    }

    public function getData(): array
    {
        return [
            'name'     => $this->name,
            'phone'    => $this->phone,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
