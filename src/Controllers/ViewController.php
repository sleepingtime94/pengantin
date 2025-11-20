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
        $this->blade = new BladeOne(
            __DIR__ . '/../../views',
            __DIR__ . '/../../cache',
            BladeOne::MODE_DEBUG
        );

        $this->users = new UserModel();
        $this->csrf = new Csrf();
        $this->files = new FileModel();
        $this->products = new ProductModel();
    }

    private function view(string $view, array $data = []): void
    {
        echo $this->blade->run($view, $data);
    }

    public function home(): void
    {
        $params = [
            'csrf_token' => $this->csrf->getToken()
        ];

        $this->view('pages.index', $params);
    }

    public function notFound(): void
    {
        $this->view('pages.404');
    }

    public function register(): void
    {
        $params = [
            'csrf_token' => $this->csrf->getToken()
        ];

        $this->view('pages.register', $params);
    }

    public function dashboard(): void
    {
        $userParam = $this->users->select($_SESSION['user_nik'])[0];
        $userProduct = $this->products->selectByID($userParam['product_id'])[0];

        $params = [
            'csrf_token' => $this->csrf->getToken(),
            'users' => $this->users->selectByID()[0] ?? [],
            'user_upload_files' => $this->files->select() ?? [],
            'user_product' => $userProduct ?? []
        ];

        $this->view('dashboard.index', $params);
    }
}
