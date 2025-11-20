<?php

namespace App\Security;

class Csrf
{
    /**
     * Inisialisasi CSRF protection
     */
    public function __construct()
    {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Inisialisasi CSRF token jika belum ada
        if (!isset($_SESSION['csrf_token'])) {
            $this->regenerateToken();
        }
    }

    /**
     * Membuat CSRF token baru
     */
    public function regenerateToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Memvalidasi CSRF token dari request
     * 
     * @return bool true jika token valid, false jika tidak
     */
    public function validateToken(): bool
    {
        // Periksa apakah token dikirimkan
        if (!isset($_POST['csrf_token'])) {
            return false;
        }

        // Periksa apakah token valid dengan perbandingan timing-safe
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            return false;
        }

        return true;
    }

    /**
     * Mendapatkan CSRF token saat ini
     * 
     * @return string CSRF token
     */
    public function getToken(): string
    {
        return $_SESSION['csrf_token'];
    }

    /**
     * Membuat HTML input field untuk CSRF token
     * 
     * @return string HTML input element
     */
    public function getTokenField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($this->getToken()) . '">';
    }
}
