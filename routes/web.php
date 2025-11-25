<?php

use Bramus\Router\Router;

$router = new Router();
$router->setNamespace('App\Controllers');

// Halaman index.
$router->before('GET', '/', 'AuthController@logged');
$router->get('/', 'ViewController@home');

// Halaman login.
$router->before('GET', '/login', 'AuthController@logged');
$router->get('/login', 'ViewController@home');

// Halaman registrasi.
$router->before('GET', '/register', 'AuthController@logged');
$router->get('/register', 'ViewController@register');

// Logout.
$router->get('/logout', 'AuthController@logout');

// Halaman dashboard.
$router->before('GET', '/dashboard', 'AuthController@verify');
$router->get('/dashboard', 'ViewController@dashboard');

// Halaman admin.
// $router->before('GET', '/admin', 'AuthController@verify');
$router->get('/admin', 'ViewController@admin');

// Halaman 404.
$router->set404('ViewController@notFound');

// Produk.
$router->before('GET|POST', '/product', 'AuthController@verify');
$router->mount('/product', function () use ($router) {
    $router->post('/register', 'ProductController@register');
    $router->post('/complete', 'ProductController@completeStatus');
});

// Autentikasi.
$router->mount('/user', function () use ($router) {
    $router->post('/login', 'UserController@login');
    $router->post('/register', 'UserController@register');
});

// Upload file.
$router->before('GET|POST', '/file', 'AuthController@verify');
$router->mount('/file', function () use ($router) {
    $router->post('/upload', 'FileController@upload');
    $router->post('/get', 'FileController@getFile');
    $router->post('/delete', 'UploadController@deleteFile');
});

$router->run();
