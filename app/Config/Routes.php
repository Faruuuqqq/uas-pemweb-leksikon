<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Leksikon::index');

$routes->get('/login', 'Auth::login');
$routes->post('/login/auth', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/register/save', 'Auth::attemptRegister');

$routes->get('/portal', 'Portal::index');
$routes->get('/portal/apiSearch', 'Portal::apiSearch'); 
$routes->get('/portal/search', 'Portal::search');

$routes->get('/leksikon', 'Leksikon::index');
$routes->get('/leksikon/detail/(:num)', 'Leksikon::detail/$1');
$routes->get('/leksikon/resetQuiz', 'Leksikon::resetQuiz');
$routes->get('/leksikon/getFavorites', 'Leksikon::getFavorites');
$routes->post('/leksikon/toggleFavorite/(:num)', 'Leksikon::toggleFavorite/$1');
$routes->post('/leksikon/checkQuiz', 'Leksikon::checkQuiz');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Admin::index');
    
    $routes->get('entri/create', 'Admin::create');
    $routes->post('entri/store', 'Admin::store');
    $routes->get('entri/edit/(:num)', 'Admin::edit/$1');
    $routes->post('entri/update/(:num)', 'Admin::update/$1');
    $routes->delete('entri/delete/(:num)', 'Admin::delete/$1');
});
