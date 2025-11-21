<?php

namespace App\Controllers;

class AuthController
{
    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function redirect(string $path): void
    {
        header("Location: {$path}");
        exit();
    }

    // Jika sudah login, jangan biarkan buka halaman login/register
    public function logged(): void
    {
        $this->startSession();

        if (!empty($_SESSION['auth_token'])) {
            session_regenerate_id(true);
            $this->redirect('/dashboard');
        }
    }

    // Middleware pengecekan login
    public function verify(): void
    {
        $this->startSession();

        if (empty($_SESSION['auth_token'])) {
            $this->redirect('/login');
        }

        session_regenerate_id(true);
    }

    // Logout
    public function logout(): void
    {
        $this->startSession();

        $_SESSION = [];
        session_unset();
        session_destroy();

        // Start sesi baru setelah dihancurkan (best practice)
        session_start();
        session_regenerate_id(true);

        $this->redirect('/login');
    }
}
