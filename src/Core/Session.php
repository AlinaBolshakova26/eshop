<?php
namespace Core;

class Session
{

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_start();
        $_SESSION = [];
        session_destroy();
    }
    
}
