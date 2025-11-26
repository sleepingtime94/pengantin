<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Security\Csrf;

class UserController
{
    private UserModel $users;
    private Csrf $csrf;

    public function __construct()
    {
        session_start();
        $this->users = new UserModel();
        $this->csrf = new Csrf();
    }

    public function login(): void
    {
        // Validasi CSRF token sebelum memproses login
        if (!$this->csrf->validateToken()) {
            $this->sendJsonResponse('error', 'CSRF token tidak valid atau hilang.');
            return;
        }

        // Validasi input
        if (empty($_POST['nik']) || empty($_POST['pass'])) {
            $this->sendJsonResponse('error', 'NIK dan Kata Sandi harus diisi.');
            return;
        }

        $turnstileResponse = $_POST['turnstileToken'];
        $secretKey = $_ENV['CF_TURNSTILE_SECRET_KEY'];
        $verifyURL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $turnstileResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($verifyURL, false, $context);
        $resultData = json_decode($result, true);

        // if (!$resultData['success']) {
        //     $this->sendJsonResponse('error', 'Verifikasi Turnstile gagal.');
        //     return;
        // }

        // Ambil data POST dan bersihkan
        $nik = trim($_POST['nik']);
        $password = trim($_POST['pass']);

        // Cek apakah NIK ada di database
        $user = $this->users->select($nik);

        if (!$user) {
            $this->sendJsonResponse('error', 'NIK atau Kata Sandi salah.');
            return;
        }

        // Verifikasi password
        if (!password_verify($password, $user[0]['user_password'])) {
            $this->sendJsonResponse('error', 'NIK atau Kata Sandi salah.');
            return;
        }

        // Setelah validasi berhasil, buat token autentikasi
        $this->setAuthentication($user[0]['id'], $nik);

        // Regenerasi CSRF token setelah login berhasil
        $this->csrf->regenerateToken();

        // Kirim respons sukses
        $this->sendJsonResponse('success', 'Login berhasil.');
    }

    public function register(): void
    {
        // Validasi CSRF token sebelum memproses registrasi
        if (!$this->csrf->validateToken()) {
            $this->sendJsonResponse('error', 'CSRF token tidak valid atau hilang.');
            return;
        }

        // Validasi input
        if (!$this->validateRegistrationInput()) {
            return;
        }

        // Bersihkan input
        $nik = trim($_POST['nik']);
        $password = password_hash(trim($_POST['pass']), PASSWORD_BCRYPT);
        $phone = trim($_POST['phone']);

        // Cek apakah NIK sudah terdaftar
        if ($this->users->select($nik)) {
            $this->sendJsonResponse('error', 'NIK sudah terdaftar.');
            return;
        }

        // Persiapkan parameter untuk disimpan
        $params = (object) [
            'nik' => $nik,
            'password' => $password,
            'phone' => $phone,
        ];

        // Simpan data ke database
        if ($this->users->create($params)) {
            // Regenerasi CSRF token setelah registrasi berhasil
            $this->csrf->regenerateToken();
            $this->sendJsonResponse('success', 'Pendaftaran berhasil.');
        } else {
            $this->sendJsonResponse('error', 'Pendaftaran gagal.');
        }
    }

    public function resetPassword()
    {
        // Validasi CSRF token sebelum memproses reset password
        if (!$this->csrf->validateToken()) {
            $this->sendJsonResponse('error', 'CSRF token tidak valid atau hilang.');
            return;
        }

        // Validasi input
        if (empty($_POST['nik'])) {
            $this->sendJsonResponse('error', 'NIK harus diisi.');
            return;
        }

        // Bersihkan input
        $nik = trim($_POST['nik']);

        // Cek apakah NIK ada di database
        if (!$this->users->select($nik)) {
            $this->sendJsonResponse('error', 'NIK tidak terdaftar.');
            return;
        }

        // Generate password baru
        $newPassword = $this->randomPassword();

        // Hash password baru
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update password di database
        $user = $this->users->select($nik);
        $userID = $user[0]['id'];
        $userPhone = $user[0]['user_phone'];
        $this->users->update($userID, ['user_password' => $hashedPassword]);

        // Kirim email notifikasi
        $this->sendWhatsapp($userPhone, $newPassword);

        $this->sendJsonResponse('success', 'Kata sandi berhasil direset. Silahkan periksa chat pada nomor Whatsapp terdaftar.');
    }

    private function randomPassword($length = 6)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand() % strlen($chars)];
        }
        return $result;
    }

    private function validateRegistrationInput(): bool
    {
        if (empty($_POST['nik']) || empty($_POST['pass']) || empty($_POST['phone'])) {
            $this->sendJsonResponse('error', 'NIK, password, dan nomor HP harus diisi.');
            return false;
        }

        if (!preg_match('/^\d{16}$/', $_POST['nik'])) {
            $this->sendJsonResponse('error', 'NIK harus berupa 16 digit angka.');
            return false;
        }

        return true;
    }

    private function setAuthentication(int $userId, int $userNik): void
    {
        // Buat token autentikasi
        $_SESSION['auth_token'] = bin2hex(random_bytes(32));
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_nik'] = $userNik;

        // Set cookie autentikasi
        setcookie('auth_token', $_SESSION['auth_token'], [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        // Regenerasi session ID untuk keamanan
        session_regenerate_id(true);
    }

    private function sendJsonResponse(string $status, string $message): void
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
        exit();
    }

    private function sendWhatsapp(string $phone, string $newPassword): void
    {
        $message = "Kata sandi sudah direset.\nSilahkan login dengan kata sandi baru ini: *$newPassword*\n\nInovasi Pengantin\nhttps://pengantin.dukcapil.tapinkab.go.id";
        $url = 'http://36.91.137.28:9000/send-message';
        $data = [
            'phone' => $phone,
            'message' => $message,
            'key' => 'dukcapil6305',
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for self-signed cert
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($result === false) {
            $this->sendJsonResponse('error', 'Gagal mengirim pesan WhatsApp: ' . $error);
        }
    }
}
