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

    public function register(): void
    {
        $obj = (object) $_POST;

        if (!$this->validateInput($obj)) {
            return;
        }

        $new_addr = $this->formatAddress($obj);

        $params = [
            'id_user' => $obj->kua,
            'lk_nik' => $obj->lk_nik,
            'lk_kk' => $obj->lk_kk,
            'lk_nama' => strtoupper($obj->lk_name),
            'lk_alamat' => strtoupper($obj->lk_addr),
            'lk_telp' => $obj->lk_phone,
            'pr_nik' => $obj->pr_nik,
            'pr_kk' => $obj->pr_kk,
            'pr_nama' => strtoupper($obj->pr_name),
            'pr_alamat' => strtoupper($obj->pr_addr),
            'pr_telp' => $obj->pr_phone,
            'st' => 0,
            'alamat_baru' => strtoupper($new_addr),
            'keterangan' => strtoupper($obj->notes)
        ];

        if (!$this->checkProductID()) {
            $this->products->update($this->getProductID(), $params);
            $this->users->update($_SESSION['user_id'], [
                'user_step' => 2,
            ]);
            $this->sendJsonResponse('success', 'Data berhasil disimpan.');
        } else {
            if ($this->products->create($params)) {
                if (!$this->checkSession()) {
                    return;
                }

                $lastID = $this->products->getLastID();
                $this->users->update($_SESSION['user_id'], [
                    'user_step' => 2,
                    'product_id' => $lastID
                ]);

                $this->sendJsonResponse('success', 'Data berhasil disimpan.');
            } else {
                $this->sendJsonResponse('error', 'Gagal menyimpan data.');
            }
        }
    }

    private function validateInput(object $obj): bool
    {
        if (!preg_match('/^\d{16}$/', $obj->lk_nik) || !preg_match('/^\d{16}$/', $obj->pr_nik)) {
            $this->sendJsonResponse('error', 'NIK harus berupa 16 digit angka.');
            return false;
        }

        if ((!empty($obj->lk_kk) && !preg_match('/^\d{16}$/', $obj->lk_kk)) || (!empty($obj->pr_kk) && !preg_match('/^\d{16}$/', $obj->pr_kk))) {
            $this->sendJsonResponse('error', 'Nomor KK harus berupa 16 digit angka.');
            return false;
        }

        return true;
    }

    private function formatAddress(object $obj): string
    {
        return "ALAMAT BARU: {$obj->addr_street} RT: {$obj->addr_rt} RW: {$obj->addr_rw} KEL/DESA: {$obj->addr_ds} KEC: {$obj->addr_kec}";
    }

    private function sendJsonResponse(string $status, string $message): void
    {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
        exit();
    }

    public function completeStatus(): void
    {
        if (!$this->checkSession()) {
            return;
        }

        $result = $this->users->update($_SESSION['user_id'], ['user_step' => 3]);

        if ($result) {
            $this->sendJsonResponse('success', 'Status berhasil diperbarui.');
        } else {
            $this->sendJsonResponse('error', 'Gagal memperbarui status.');
        }
    }

    public function editFormulir(): void
    {
        if (!$this->checkSession()) {
            return;
        }

        $getStatusProduct = $this->getStatusProduct();

        if ($getStatusProduct > 0) {
            $this->sendJsonResponse('success', 'Formulir sudah diverifikasi.');
        } else {
            $result = $this->users->update($_SESSION['user_id'], ['user_step' => 1]);
            if ($result) {
                $this->sendJsonResponse('success', 'Status berhasil diperbarui.');
            } else {
                $this->sendJsonResponse('error', 'Gagal memperbarui status.');
            }
        }
    }

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

    private function getStatusProduct()
    {
        $productID = $this->users->selectByID($_SESSION['user_id']);
        $productID = $productID[0]['product_id'];
        $getStatusProduct = $this->products->selectByID($productID);
        return $getStatusProduct[0]['st'];
    }

    private function checkProductID()
    {
        $productID = $this->users->selectByID($_SESSION['user_id']);
        $productID = $productID[0]['product_id'];

        if (!$productID) {
            return false;
        }
    }

    private function getProductID()
    {
        if (!$this->checkSession()) {
            return null;
        }

        $userData = $this->users->selectByID($_SESSION['user_id']);

        if (empty($userData) || !isset($userData[0]['product_id'])) {
            return null;
        }

        return $userData[0]['product_id'];
    }
}
