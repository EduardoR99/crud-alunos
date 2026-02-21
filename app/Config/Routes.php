<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function (RouteCollection $routes): void {
    // Auth (público, com rate limiting)
    $routes->post('auth/register', 'AuthController::register', ['filter' => 'ratelimit:3,1']);
    $routes->post('auth/login', 'AuthController::login', ['filter' => 'ratelimit:5,1']);
    $routes->post('auth/logout', 'AuthController::logout');
    $routes->get('auth/me', 'AuthController::me');

    // Rotas protegidas por JWT
    $routes->group('', ['filter' => 'jwt'], static function (RouteCollection $routes): void {
        $routes->get('students', 'StudentController::index');
        $routes->get('students/(:num)', 'StudentController::show/$1');
        $routes->post('students', 'StudentController::create');
        $routes->put('students/(:num)', 'StudentController::update/$1');
        $routes->delete('students/(:num)', 'StudentController::delete/$1');
    });
});
