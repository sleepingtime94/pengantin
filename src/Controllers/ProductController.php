<?php

namespace App\Controllers;

use Exception;
use App\Models\ProductModel;
use App\Models\UserModel;

class ProductController
{
    private ProductModel $products;
    private UserModel $users;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->users = new UserModel();
    }

    /**
     * Mendaftarkan atau memperbarui data produk berdasarkan input dari form.
     * Metode ini memvalidasi input, memformat alamat, dan kemudian membuat entri baru
     * atau memperbarui entri yang sudah ada di database.
     */
    public function register(): void
    {
        // Mengubah nama variabel menjadi lebih deskriptif
        $requestData = (object) $_POST;

        if (!$this->validateInput($requestData)) {
            return;
        }

        $newAddress = $this->formatAddress($requestData);

        // Menambahkan komentar untuk menjelaskan singkatan yang tidak jelas
        $params = [
            'id_user' => $requestData->kua,
            'lk_nik' => $requestData->lk_nik, // lk = Laki-laki (Male)
            'lk_kk' => $requestData->lk_kk,
            'lk_nama' => strtoupper($requestData->lk_name),
            'lk_alamat' => strtoupper($requestData->lk_addr),
            'lk_telp' => $requestData->lk_phone,
            'pr_nik' => $requestData->pr_nik, // pr = Perempuan (Female)
            'pr_kk' => $requestData->pr_kk,
            'pr_nama' => strtoupper($requestData->pr_name),
            'pr_alamat' => strtoupper($requestData->pr_addr),
            'pr_telp' => $requestData->pr_phone,
            'st' => 0, // st = Status
            'alamat_baru' => strtoupper($newAddress),
            'keterangan' => strtoupper($requestData->notes)
        ];

        // Memastikan sesi ada sebelum melanjutkan
        if (!$this->checkSession()) {
            return;
        }

        $productId = $this->getProductID();
        $isSuccess = false;

        // Perbaikan logika: jika product ID ada, maka update. Jika tidak, maka create.
        if ($productId) {
            $isSuccess = $this->products->update($productId, $params);
        } else {
            $isSuccess = $this->products->create($params);
            if ($isSuccess) {
                $productId = $this->products->getLastID();
            }
        }

        if ($isSuccess) {
            // Pembaruan status user dilakukan di luar if/else untuk menghindari pengulangan kode (DRY Principle)
            $this->users->update($_SESSION['user_id'], [
                'user_step' => 2,
                'product_id' => $productId // Hanya update product_id jika baru dibuat
            ]);
            $this->sendJsonResponse('success', 'Data berhasil disimpan.');
        } else {
            $this->sendJsonResponse('error', 'Gagal menyimpan data.');
        }
    }

    /**
     * Memvalidasi input NIK dan KK dari request data.
     *
     * @param object $requestData Data yang dikirim dari form.
     * @return bool Mengembalikan true jika valid, false jika tidak.
     */
    private function validateInput(object $requestData): bool
    {
        if (!preg_match('/^\d{16}$/', $requestData->lk_nik) || !preg_match('/^\d{16}$/', $requestData->pr_nik)) {
            $this->sendJsonResponse('error', 'NIK harus berupa 16 digit angka.');
            return false;
        }

        if ((!empty($requestData->lk_kk) && !preg_match('/^\d{16}$/', $requestData->lk_kk)) || (!empty($requestData->pr_kk) && !preg_match('/^\d{16}$/', $requestData->pr_kk))) {
            $this->sendJsonResponse('error', 'Nomor KK harus berupa 16 digit angka.');
            return false;
        }

        return true;
    }

    /**
     * Memformat alamat menjadi satu string yang terstruktur.
     *
     * @param object $requestData Data yang dikirim dari form.
     * @return string Alamat yang sudah diformat.
     */
    private function formatAddress(object $requestData): string
    {
        return "ALAMAT BARU: {$requestData->addr_street} RT: {$requestData->addr_rt} RW: {$requestData->addr_rw} KEL/DESA: {$requestData->addr_ds} KEC: {$requestData->addr_kec}";
    }

    /**
     * Mengirimkan response dalam format JSON dan menghentikan eksekusi skrip.
     *
     * @param string $status Status dari response ('success' atau 'error').
     * @param string $message Pesan yang akan dikirim.
     */
    private function sendJsonResponse(string $status, string $message): void
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
        exit();
    }

    /**
     * Memperbarui status user menjadi 'selesai' (step 3).
     */
    public function completeStatus(): void
    {
        if (!$this->checkSession()) {
            return;
        }

        $result = $this->users->update($_SESSION['user_id'], ['user_step' => 3]);

        $this->sendJsonResponseFromResult($result, 'Status berhasil diperbarui.', 'Gagal memperbarui status.');
    }

    /**
     * Mengembalikan status user ke 'edit formulir' (step 1) jika formulir belum diverifikasi.
     */
    public function editFormulir(): void
    {
        if (!$this->checkSession()) {
            return;
        }

        $productStatus = $this->getStatusProduct();

        if ($productStatus > 0) {
            $this->sendJsonResponse('success', 'Formulir sudah diverifikasi dan tidak dapat diedit.');
        } else {
            $result = $this->users->update($_SESSION['user_id'], ['user_step' => 1]);
            $this->sendJsonResponseFromResult($result, 'Status berhasil diperbarui.', 'Gagal memperbarui status.');
        }
    }

    /**
     * Memeriksa apakah sesi pengguna ada dan valid.
     *
     * @return bool Mengembalikan true jika sesi valid, false jika tidak.
     */
    private function checkSession(): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->sendJsonResponse('error', 'User ID not found in session');
            return false;
        }

        return true;
    }

    /**
     * Mengambil status produk dari database berdasarkan user yang login.
     *
     * @return int Status produk.
     */
    private function getStatusProduct(): int
    {
        $productId = $this->getProductID();
        if (!$productId) {
            return 0; // Asumsikan status 0 jika tidak ada produk
        }

        $productData = $this->products->selectByID($productId);
        // Penambahan pemeriksaan untuk menghindari error jika data tidak ditemukan
        return !empty($productData) ? (int)$productData[0]['st'] : 0;
    }

    /**
     * Memeriksa apakah product ID ada untuk user yang sedang login.
     * Metode ini telah diperbaiki untuk mengembalikan nilai boolean yang jelas.
     *
     * @return bool True jika product ID ada, false jika tidak.
     */
    private function checkProductID(): bool
    {
        $productId = $this->getProductID();
        return !empty($productId);
    }

    /**
     * Mengambil ID produk dari user yang sedang login.
     *
     * @return int|null Product ID jika ditemukan, null jika tidak.
     */
    private function getProductID(): ?int
    {
        if (!$this->checkSession()) {
            return null;
        }

        $userData = $this->users->selectByID($_SESSION['user_id']);
        
        // Penambahan pemeriksaan untuk menghindari error "Undefined offset"
        if (empty($userData) || !isset($userData[0]['product_id'])) {
            return null;
        }

        return (int)$userData[0]['product_id'];
    }
    
    /**
     * Helper method untuk mengirimkan response JSON berdasarkan hasil operasi database.
     *
     * @param bool $result Hasil dari operasi database (true/false).
     * @param string $successMessage Pesan jika berhasil.
     * @param string $errorMessage Pesan jika gagal.
     */
    private function sendJsonResponseFromResult(bool $result, string $successMessage, string $errorMessage): void
    {
        if ($result) {
            $this->sendJsonResponse('success', $successMessage);
        } else {
            $this->sendJsonResponse('error', $errorMessage);
        }
    }
}