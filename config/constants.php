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