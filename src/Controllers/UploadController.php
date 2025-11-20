<?php

namespace App\Controllers;

use Exception;
use RuntimeException;
use voku\helper\UTF8;
use Symfony\Component\Stopwatch\Stopwatch;
use App\Security\Csrf;
use App\Models\FileModel;

class UploadController
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;
    private const UPLOAD_DIRECTORY = '../uploads/';

    private $stopwatch;
    private Csrf $csrf;
    private FileModel $fileModel;

    public function __construct()
    {
        $this->ensureUploadDirectoryExists();
        $this->stopwatch = new Stopwatch();
        $this->csrf = new Csrf();
        $this->fileModel = new FileModel();
    }

    private function ensureUploadDirectoryExists(): void
    {
        if (!is_dir(self::UPLOAD_DIRECTORY)) {
            if (!mkdir(self::UPLOAD_DIRECTORY, 0755, true) && !is_dir(self::UPLOAD_DIRECTORY)) {
                throw new RuntimeException('Gagal membuat direktori upload');
            }
        }
        if (!is_writable(self::UPLOAD_DIRECTORY)) {
            throw new RuntimeException('Direktori upload tidak dapat ditulis');
        }
    }

    public function handleUpload(): array
    {
        try {
            if (!$this->csrf->validateToken()) {
                return [
                    'status' => 'error',
                    'message' => 'CSRF token tidak valid atau hilang.'
                ];
            }

            if (!$this->isValidUpload()) {
                return [
                    'status' => 'error',
                    'message' => 'Tidak ada file yang diupload atau terjadi kesalahan upload.'
                ];
            }

            $file = $_FILES['file'];
            $event = $this->stopwatch->start('file_upload');

            // Validasi file
            if (!$this->validateFile($file)) {
                return [
                    'status' => 'error',
                    'message' => $this->getValidationErrorMessage($file)
                ];
            }

            // Generate nama file yang unik
            $safeFileName = $this->generateUniqueFileName($file['name']);
            $destination = self::UPLOAD_DIRECTORY . $safeFileName;

            // Pindahkan file
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $event->stop();

                $this->fileModel->create([
                    'user_id' => $_SESSION['user_id'],
                    'file_path' => $safeFileName,
                    'file_category' => $_POST['category'],
                ]);

                return [
                    'status' => 'success',
                    'message' => sprintf(
                        "File berhasil diupload! Waktu upload: %d ms",
                        $event->getDuration()
                    ),
                    'filePath' => $destination
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Gagal mengupload file.'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    private function isValidUpload(): bool
    {
        return isset($_FILES['file']) &&
            $_FILES['file']['error'] === UPLOAD_ERR_OK &&
            is_uploaded_file($_FILES['file']['tmp_name']);
    }

    private function validateFile(array $file): bool
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($extension, self::ALLOWED_EXTENSIONS) &&
            $file['size'] <= self::MAX_FILE_SIZE &&
            $file['size'] > 0;
    }

    private function getValidationErrorMessage(array $file): string
    {
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return 'Ukuran file terlalu besar, maksimal adalah 1MB.';
        }
        if ($file['size'] === 0) {
            return 'File kosong tidak diizinkan.';
        }
        return 'Tipe file tidak diizinkan.';
    }

    private function generateUniqueFileName(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = UTF8::to_ascii(pathinfo($originalName, PATHINFO_FILENAME));
        $hashName = md5($baseName);
        $random = bin2hex(random_bytes(4));

        return "file_{$hashName}_{$random}.{$extension}";
    }

    public function deleteFile()
    {
        try {
            // Validasi CSRF token
            if (!$this->csrf->validateToken()) {
                http_response_code(403);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid CSRF token'
                ]);
                return;
            }

            if (!isset($_POST['file_id']) || !isset($_POST['file_path'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            $fileId = $_POST['file_id'];
            $filePath = $_POST['file_path'];
            $fullFilePath = self::UPLOAD_DIRECTORY . $filePath;
            $fileDeletionSuccessful = false;
            $databaseDeletionSuccessful = false;

            // Cek apakah file ada di sistem
            if (file_exists($fullFilePath)) {
                // Hapus file dari sistem
                if (unlink($fullFilePath)) {
                    $fileDeletionSuccessful = true;
                } else {
                    error_log("Gagal menghapus file: " . $fullFilePath);
                }
            } else {
                // Jika file tidak ada, anggap sukses dari sisi file system
                $fileDeletionSuccessful = true;
                error_log("File tidak ditemukan: " . $fullFilePath);
            }

            // Hapus entri dari database
            $databaseDeletionSuccessful = $this->fileModel->delete($fileId);
            if (!$databaseDeletionSuccessful) {
                error_log("Gagal menghapus record database untuk file ID: " . $fileId);
            }

            $response = [];

            if ($fileDeletionSuccessful && $databaseDeletionSuccessful) {
                $response = [
                    'status' => 'success',
                    'message' => 'File berhasil dihapus dari sistem dan record database.'
                ];
            } elseif ($fileDeletionSuccessful && !$databaseDeletionSuccessful) {
                $response = [
                    'status' => 'warning',
                    'message' => 'File berhasil dihapus dari sistem, tetapi gagal menghapus record dari database.'
                ];
            } elseif (!$fileDeletionSuccessful && $databaseDeletionSuccessful) {
                $response = [
                    'status' => 'warning',
                    'message' => 'Gagal menghapus file dari sistem, tetapi record database berhasil dihapus (inkonsistensi data!).'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Gagal menghapus file dari sistem dan record database.'
                ];
            }

            // Set proper headers and return JSON response
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            error_log("Error saat menghapus file: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat menghapus file'
            ]);
        }
    }
}
