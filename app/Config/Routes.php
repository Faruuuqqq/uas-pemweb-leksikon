<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Route Utama ---
$routes->get('/', 'Leksikon::index');

// --- Grup untuk Fungsionalitas Leksikon ---
$routes->group('leksikon', static function ($routes) {
    $routes->get('detail/(:num)', 'Leksikon::detail/$1');
    $routes->get('search', 'Leksikon::search');
    $routes->post('cek_kuis', 'Leksikon::checkQuiz');
    $routes->get('resetQuiz', 'Leksikon::resetQuiz');
    $routes->get('get_favorites', 'Leksikon::getFavorites');
    $routes->post('toggleFavorite/(:num)', 'Leksikon::toggleFavorite/$1');
    $routes->get('externalSearch', 'Leksikon::displayExternalSearch');
    $routes->get('externalSearch/query', 'Leksikon::queryExternalSearch');
});


// --- Grup untuk Halaman Admin (CRUD) ---
$routes->group('admin', static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('new', 'Admin::create');
    $routes->post('new', 'Admin::store');
    $routes->get('edit/(:num)', 'Admin::edit/$1');
    $routes->post('edit/(:num)', 'Admin::update/$1');
    $routes->delete('delete/(:num)', 'Admin::delete/$1');
});

// --- Authentication Routes ---
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');
$routes->get('profile', 'Auth::profile');
$routes->post('profile/update', 'Auth::updateProfile');

// --- Portal Routes ---
$routes->get('/portal', 'Portal::index');
$routes->get('/portal/search', 'Portal::search');
$routes->get('portal/api-search', 'Portal::api_search');
