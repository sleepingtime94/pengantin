<?php

namespace App\Controllers;

use eftec\bladeone\BladeOne;
use App\Security\Csrf;
use App\Models\UserModel;
use App\Models\FileModel;
use App\Models\ProductModel;

class ViewController
{
    private BladeOne $blade;
    private Csrf $csrf;
    private UserModel $users;
    private FileModel $files;
    private ProductModel $products;

    public function __construct()
    {
        $base = __DIR__ . '/../../';

        $this->blade = new BladeOne(
            $base . 'views',
            $base . 'cache',
            BladeOne::MODE_DEBUG
        );

        $this->csrf     = new Csrf();
        $this->users    = new UserModel();
        $this->files    = new FileModel();
        $this->products = new ProductModel();
    }

    private function view(string $view, array $data = []): void
    {
        echo $this->blade->run($view, $data);
    }

    private function withCsrf(array $data = []): array
    {
        return array_merge(['csrf_token' => $this->csrf->getToken()], $data);
    }

    public function home(): void
    {
        $this->view('pages.index', $this->withCsrf());
    }

    public function notFound(): void
    {
        $this->view('pages.404');
    }

    public function register(): void
    {
        $this->view('pages.register', $this->withCsrf());
    }

    public function admin(): void
    {
        $params = [
            'users' => $this->users->show() ?? [],
        ];
        
        $this->view('pages.admin', $params);
    }

    public function dashboard(): void
    {
        // Ambil user berdasarkan session NIK
        $nik   = $_SESSION['user_nik'] ?? null;
        $user  = $nik ? ($this->users->select($nik)[0] ?? null) : null;

        // Cegah error kalau user tidak ditemukan
        if (!$user) {
            $this->view('pages.404');
            return;
        }

        // Ambil product (aman dari NULL)
        $productId = $user['product_id'] ?? null;
        $product   = $productId ? ($this->products->selectByID((int) $productId)[0] ?? null) : null;

        $params = $this->withCsrf([
            'users'             => $user,
            'user_upload_files' => $this->files->select() ?? [],
            'user_product'      => $product ?? [],
        ]);

        $this->view('dashboard.index', $params);
    }
}
