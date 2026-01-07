<?php

namespace App\Session;

class SessionHelper
{
    public function __construct()
    {
        session_start();
    }
    
    public function setValues(array $data): void
    {
        foreach ($data as $item) {
            $_SESSION[$item['key']] = $item['value'];
        }
    }

    public function getUserId(): int
    {
        $userId = $_SESSION['userid'] ?? 0;
        return $userId;
    }

    public function getUsername(): string
    {
        $username = $_SESSION['username'] ?? '';
        return $username;
    }
    public function getIsAdmin(): bool
    {
        $isAdmin = $_SESSION['admin'] ?? false;
        return $isAdmin;
    }
}