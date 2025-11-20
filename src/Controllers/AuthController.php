<?php

namespace App\Controllers;

class AuthController
{
    // Memastikan sesi dimulai sebelum digunakan
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mengalihkan pengguna yang sudah login ke dashboard.
    public function logged(): void
    {
        $this->startSession();

        if (!empty($_SESSION['auth_token'])) {
            session_regenerate_id(true); // Perbarui session ID
            header('Location: /dashboard');
            exit();
        }
    }

    // Memverifikasi apakah pengguna sudah terautentikasi.
    public function verify(): void
    {
        $this->startSession();

        if (empty($_SESSION['auth_token'])) {
            header('Location: /login');
            exit();
        }

        session_regenerate_id(true); // Perbarui session ID setelah verifikasi
    }

    // Logout pengguna dan menghancurkan sesi.
    public function logout(): void
    {
        $this->startSession();

        // Hapus semua variabel sesi
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Regenerasi ID sesi setelah logout untuk keamanan tambahan
        session_start();
        session_regenerate_id(true);

        header('Location: /login');
        exit();
    }
}
