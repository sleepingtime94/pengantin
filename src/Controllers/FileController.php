<?php

namespace App\Controllers;

use App\Controllers\UploadController;

class FileController
{

    public function upload()
    {
        $upload = new UploadController();
        $result = $upload->handleUpload();
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function getFile()
    {
        $filePath = __DIR__ . '/../../uploads/' . $_POST['path'];

        if (file_exists($filePath)) {
            // Dapatkan tipe MIME file
            $mimeType = mime_content_type($filePath);

            // Dapatkan ukuran file
            $fileSize = filesize($filePath);

            // Atur header HTTP
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . $fileSize);
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');

            // Kirim isi file sebagai blob
            readfile($filePath);
            exit;
        } else {
            // Jika file tidak ditemukan, kirim respons error
            header('HTTP/1.0 404 Not Found');
            echo "File does not exist: " . $_POST['path'];
        }
    }
}
