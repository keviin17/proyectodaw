<?php
// config/constants.php
// Constantes globales de la aplicación Velora Shop

define('APP_NAME', 'Velora Shop');
define('BASE_URL', 'http://localhost/velora_shop/public');
define('BASE_PATH', dirname(__DIR__));

// Rutas de assets
define('ASSETS_PATH', BASE_PATH . '/assets');
define('IMG_PRODUCTS_PATH', ASSETS_PATH . '/img/products');
define('IMG_PRODUCTS_URL', BASE_URL . '/../assets/img/products');

// Subcarpetas de imágenes por género (deben coincidir con el enum de categoria.genero)
define('IMG_PRODUCTS_SUBFOLDERS', [
    'hombre'  => IMG_PRODUCTS_PATH . '/hombre',
    'mujer'   => IMG_PRODUCTS_PATH . '/mujer',
    'niño'    => IMG_PRODUCTS_PATH . '/nino',   // carpeta física: nino (sin tilde)
    'unisex'  => IMG_PRODUCTS_PATH . '/unisex',
]);
define('IMG_PRODUCTS_SUBFOLDERS_URL', [
    'hombre'  => IMG_PRODUCTS_URL . '/hombre',
    'mujer'   => IMG_PRODUCTS_URL . '/mujer',
    'niño'    => IMG_PRODUCTS_URL . '/nino',
    'unisex'  => IMG_PRODUCTS_URL . '/unisex',
]);

// Roles de usuario
define('ROL_CLIENTE', 'cliente');
define('ROL_ADMIN',   'admin');

// Estados de pedido
define('ESTADOS_PEDIDO', [
    'pendiente',
    'procesando',
    'enviado',
    'entregado',
    'cancelado',
]);

// Tallas disponibles
define('TALLAS', ['XS', 'S', 'M', 'L', 'XL', 'XXL']);