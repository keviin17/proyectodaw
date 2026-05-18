<?php
// views/layout/header.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Cart.php';

$cartCount = 0;
if (!empty($_SESSION['usuario_id'])) {
    $cartModel = new Cart();
    $cartCount = $cartModel->contarItems($_SESSION['usuario_id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Velora Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
    <!-- Anti-parpadeo: aplicar clase dark-mode al body ANTES de pintar -->
    <script>
    (function(){
        var t = localStorage.getItem('velora-tema');
        // Si no hay preferencia guardada, usar preferencia del sistema
        var dark = t === 'dark' || (!t && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (dark) {
            // Añadimos la clase directamente al html para que body la herede antes de cargarse
            document.documentElement.classList.add('dark-mode-pending');
        }
    })();
    </script>
    <style>
        /* Previene parpadeo: si hay dark pendiente, aplicamos bg oscuro de inmediato */
        html.dark-mode-pending body,
        html.dark-mode-pending {
            background-color: #0d0d18 !important;
            color: #ddd5f5 !important;
        }
    </style>
</head>
<body>

<script>
// Mover la clase a body en cuanto exista
(function(){
    if (document.documentElement.classList.contains('dark-mode-pending')) {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.remove('dark-mode-pending');
    }
})();
</script>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= BASE_URL ?>/">
            <i class="bi bi-bag-heart-fill text-warning me-1"></i>Velora Shop
        </a>
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Catálogo</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/?action=catalogo">Todos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/?action=catalogo&amp;genero=hombre">Hombre</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/?action=catalogo&amp;genero=mujer">Mujer</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/?action=catalogo&amp;genero=ni%C3%B1o">Niño</a></li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex me-2" action="<?= BASE_URL ?>/" method="GET">
                <input type="hidden" name="action" value="buscar">
                <input class="form-control form-control-sm me-1" type="search"
                       name="q" placeholder="Buscar..."
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                       style="width:180px;">
                <button class="btn btn-sm btn-outline-warning" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <ul class="navbar-nav">
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>/?action=carrito">
                            <i class="bi bi-cart3 fs-5"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                    <?= $cartCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?action=wishlist" title="Lista de deseos">
                            <i class="bi bi-heart fs-5"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (($_SESSION['usuario_rol'] ?? '') === 'admin'): ?>
                                <li><a class="dropdown-item text-danger fw-bold" href="<?= BASE_URL ?>/?action=admin_dashboard">
                                    <i class="bi bi-speedometer2 me-1"></i>Panel Admin</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?action=perfil">
                                <i class="bi bi-person me-1"></i>Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?action=mis_pedidos">
                                <i class="bi bi-bag me-1"></i>Mis Pedidos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/?action=logout">
                                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?action=login">
                            <i class="bi bi-person me-1"></i>Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm text-dark px-3 ms-2 nav-link"
                           href="<?= BASE_URL ?>/?action=register">Registrarse</a>
                    </li>
                <?php endif; ?>
                <!-- Botón tema oscuro/claro -->
                <li class="nav-item ms-2">
                    <button id="btnTema" class="btn btn-sm btn-outline-warning rounded-circle"
                            title="Cambiar tema" style="width:36px;height:36px;padding:0;">
                        <i class="bi bi-moon-fill" id="iconTema"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0" role="alert">
        <div class="container"><i class="bi bi-check-circle me-1"></i>
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
        <div class="container"><i class="bi bi-exclamation-triangle me-1"></i>
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
