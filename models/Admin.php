<?php

class Admin
{
    private $pdo;
    private $credentials = [
        'admin@gmail.com' => 'admin11'
    ];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function authenticate(string $email, string $password): bool
    {
        return isset($this->credentials[$email]) && $this->credentials[$email] === $password;
    }

    public function login(string $email): void
    {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Superviseur';
        $_SESSION['admin_email'] = $email;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public function isLogged(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public function getName(): string
    {
        return $_SESSION['admin_name'] ?? 'Admin';
    }

    public function getEmail(): string
    {
        return $_SESSION['admin_email'] ?? '';
    }

    
}
