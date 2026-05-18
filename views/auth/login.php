<?php
// views/auth/login.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Iniciar Sesión';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <title><?= $pageTitle ?> — Velora Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <!-- Logo / título -->
            <div class="text-center mb-4">
                <a href="<?= BASE_URL ?>/" class="text-decoration-none text-dark">
                    <h2 class="fw-bold">
                        <i class="bi bi-bag-heart-fill text-warning me-2"></i>Velora Shop
                    </h2>
                </a>
                <p class="text-muted small">Accede a tu cuenta</p>
            </div>

            <!-- Mensajes de sesión -->
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Formulario de login -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Iniciar sesión</h5>

                    <form action="<?= BASE_URL ?>/?action=login" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="tu@email.com" required autofocus
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <div class="invalid-feedback">Introduce un email válido.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="contraseña" class="form-label small fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="contraseña" name="contraseña"
                                       placeholder="Tu contraseña" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePass"
                                        title="Mostrar/ocultar contraseña">
                                    <i class="bi bi-eye" id="togglePassIcon"></i>
                                </button>
                                <div class="invalid-feedback">La contraseña es obligatoria.</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-3 small text-muted">
                ¿No tienes cuenta?
                <a href="<?= BASE_URL ?>/?action=register" class="text-decoration-none fw-semibold">
                    Regístrate gratis
                </a>
            </p>
            <p class="text-center">
                <a href="<?= BASE_URL ?>/" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Volver a la tienda
                </a>
            </p>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= ASSETS_URL ?>/js/main.js"></script>
<script>
    // Mostrar/ocultar contraseña
    document.getElementById('togglePass').addEventListener('click', function () {
        const input = document.getElementById('contraseña');
        const icon  = document.getElementById('togglePassIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
</body>
</html>