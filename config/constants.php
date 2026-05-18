<?php
// config/constants.php
// Constantes globales de la aplicación Velora Shop

define('APP_NAME', 'Velora Shop');

// ── BASE_URL dinámica ─────────────────────────────────────────────────────────
// Funciona en local (XAMPP/WAMP) y en producción sin modificar nada.
// En producción con DocumentRoot apuntando a /public, SCRIPT_NAME será /index.php
// y BASE_URL quedará como https://proyectodaw.org.es
// En local con /velora_shop/public/index.php, BASE_URL será http://localhost/velora_shop/public
if (!defined('BASE_URL')) {
    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Directorio donde vive index.php (sin el nombre del archivo)
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
    define('BASE_URL', $scheme . '://' . $host . $scriptDir);
}
// ─────────────────────────────────────────────────────────────────────────────

define('BASE_PATH', dirname(__DIR__));

// Rutas de assets (filesystem)
define('ASSETS_PATH', BASE_PATH . '/assets');
define('IMG_PRODUCTS_PATH', ASSETS_PATH . '/img/products');

// ASSETS_URL: sube un nivel desde /public hacia /assets (válido en local y producción).
// En producción si el DocumentRoot ES /public, BASE_URL no tendrá subfolder;
// el servidor debe tener un Alias/symlink o estar configurado para servir /assets.
// La notación /../ la resuelve el navegador correctamente en ambos entornos.
define('ASSETS_URL', BASE_URL . '/../assets');
define('IMG_PRODUCTS_URL', ASSETS_URL . '/img/products');

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