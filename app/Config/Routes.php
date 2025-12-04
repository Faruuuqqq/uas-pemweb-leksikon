<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Leksikon::index');

// Auth Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login/auth', 'Auth::attemptLogin'); // Proses Login
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register/save', 'Auth::attemptRegister');

// Portal Pencarian (Public)
$routes->get('/portal', 'Portal::index');
$routes->get('/portal/api-search', 'Portal::api_search'); // AJAX API
$routes->get('/portal/search', 'Portal::search');

// Leksikon Public (Search & Detail)
$routes->get('/leksikon', 'Leksikon::index');
$routes->get('/leksikon/detail/(:num)', 'Leksikon::detail/$1');
$routes->get('/leksikon/resetQuiz', 'Leksikon::resetQuiz');
$routes->get('/leksikon/getFavorites', 'Leksikon::getFavorites');
$routes->post('/leksikon/toggleFavorite/(:num)', 'Leksikon::toggleFavorite/$1');
$routes->post('/leksikon/checkQuiz', 'Leksikon::checkQuiz');

// Admin Routes (Harus Login)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    // Dashboard Admin
    $routes->get('/', 'Admin::index'); // Menampilkan list entri
    
    // CRUD Entri
    $routes->get('entri/create', 'Admin::create'); // Form Tambah
    $routes->post('entri/store', 'Admin::store');  // Proses Simpan
    $routes->get('entri/edit/(:num)', 'Admin::edit/$1'); // Form Edit
    $routes->post('entri/update/(:num)', 'Admin::update/$1'); // Proses Update
    $routes->delete('entri/delete/(:num)', 'Admin::delete/$1'); // Proses Hapus (Method DELETE)
});
