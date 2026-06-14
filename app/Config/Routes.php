<?php

namespace Config;

$routes = Services::routes();

// Rutas públicas (frontend)
$routes->get('/', 'Shop::index');
$routes->get('/categoria/(:segment)', 'Shop::category/$1');
$routes->get('/producto/(:segment)', 'Shop::product/$1');
$routes->get('/buscar', 'Shop::search');
$routes->get('/contacto', 'Contact::index');
$routes->post('/contacto/enviar', 'Contact::send');
$routes->get('/acerca', 'About::index');
$routes->get('/sucursales', 'Branches::index');

// Rutas de autenticación para clientes
$routes->get('/login', 'Auth::login');
$routes->get('/registro', 'Auth::register');
$routes->post('/registro', 'Auth::attemptRegister');
$routes->get('/logout', 'Auth::logout');
$routes->get('/verificar/(:any)', 'Auth::verify/$1');
// OTP Authentication
$routes->post('/auth/send-otp', 'Auth::sendOtp');
$routes->get('/auth/verify', 'Auth::verifyOtpForm');
$routes->post('/auth/verify-otp', 'Auth::verifyOtp');

// Rutas del carrito
$routes->get('/carrito', 'Cart::index');
$routes->post('/carrito/agregar', 'Cart::add');
$routes->post('/carrito/actualizar', 'Cart::update');
$routes->post('/carrito/eliminar', 'Cart::remove');
$routes->get('/carrito/vaciar', 'Cart::clear');
$routes->get('/carrito/count', 'Cart::count');

// Perfil de usuario (requiere login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/perfil', 'Auth::profile');
    $routes->post('/perfil/actualizar', 'Auth::updateProfile');
    $routes->get('/mis-pedidos', 'Auth::orders');
    $routes->get('/mis-favoritos', 'Auth::favorites');
    $routes->post('/favoritos/agregar', 'Auth::addFavorite');
    $routes->post('/favoritos/remover', 'Auth::removeFavorite');

    $routes->get('/mis-direcciones', 'Auth::addresses');
    $routes->post('/direccion/agregar', 'Auth::addAddress');
    $routes->post('/direccion/actualizar/(:num)', 'Auth::updateAddress/$1');
    $routes->get('/direccion/eliminar/(:num)', 'Auth::deleteAddress/$1');
    $routes->get('/direccion/default/(:num)', 'Auth::setDefaultAddress/$1');
});

// Checkout (requiere login o invitado)
$routes->get('/checkout', 'Checkout::index');
$routes->post('/checkout/procesar', 'Checkout::process');
$routes->get('/checkout/exito/(:num)', 'Checkout::success/$1');
$routes->post('/checkout/webhook', 'Checkout::webhook');
$routes->get('/checkout/webhook', 'Checkout::webhook');
$routes->post('/checkout/process-payment', 'Checkout::processPayment');
$routes->post('/checkout/save-shipping', 'Checkout::saveShipping');

// Rutas de la API (v1)
/* $routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function($routes) {
    // Registro de aplicaciones (público, genera API Key)
    $routes->post('register', 'Auth::register');
    
    // Endpoint público para obtener referencia de la API
    $routes->get('reference', 'ApiReference::index');
    
    // Rutas protegidas por API Key
    $routes->group('', ['filter' => 'api_auth'], function($routes) {
        // Categorías
        $routes->get('categories', 'Categories::index');
        $routes->get('categories/(:num)', 'Categories::show/$1');
        $routes->post('categories', 'Categories::create', ['filter' => 'role:STORE_OWNER,LP_SUPER_ADMIN']);
        $routes->put('categories/(:num)', 'Categories::update/$1', ['filter' => 'role:STORE_OWNER,LP_SUPER_ADMIN']);
        $routes->delete('categories/(:num)', 'Categories::delete/$1', ['filter' => 'role:STORE_OWNER,LP_SUPER_ADMIN']);
        
        // Productos (similar)
        $routes->get('products', 'Products::index');
        $routes->get('products/(:num)', 'Products::show/$1');
        $routes->post('products', 'Products::create', ['filter' => 'role:STORE_OWNER,STORE_ADMIN']);
        $routes->put('products/(:num)', 'Products::update/$1', ['filter' => 'role:STORE_OWNER,STORE_ADMIN']);
        $routes->delete('products/(:num)', 'Products::delete/$1', ['filter' => 'role:STORE_OWNER']);
        
        // Configuración de la tienda (solo LP_SUPER_ADMIN)
        $routes->get('config', 'Config::index', ['filter' => 'role:LP_SUPER_ADMIN']);
        $routes->put('config', 'Config::update', ['filter' => 'role:LP_SUPER_ADMIN']);
    });
}); */