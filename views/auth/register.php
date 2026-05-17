<?php
// views/auth/register.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Crear Cuenta';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?> — Velora Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <!-- Logo / título -->
            <div class="text-center mb-4">
                <a href="<?= BASE_URL ?>/" class="text-decoration-none text-dark">
                    <h2 class="fw-bold">
                        <i class="bi bi-bag-heart-fill text-warning me-2"></i>Velora Shop
                    </h2>
                </a>
                <p class="text-muted small">Crea tu cuenta gratis</p>
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

            <!-- Formulario de registro -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Crear cuenta</h5>

                    <form action="<?= BASE_URL ?>/?action=register" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-semibold">Nombre completo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                       placeholder="Tu nombre" required minlength="2" autofocus
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                                <div class="invalid-feedback">El nombre es obligatorio (mín. 2 caracteres).</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="tu@email.com" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <div class="invalid-feedback">Introduce un email válido.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contraseña" class="form-label small fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="contraseña" name="contraseña"
                                       placeholder="Mínimo 6 caracteres" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="togglePass1"
                                        title="Mostrar/ocultar">
                                    <i class="bi bi-eye" id="togglePass1Icon"></i>
                                </button>
                                <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="contraseña2" class="form-label small fw-semibold">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="contraseña2" name="contraseña2"
                                       placeholder="Repite la contraseña" required minlength="6">
                                <div class="invalid-feedback">Este campo es obligatorio.</div>
                            </div>
                            <div id="passMatchMsg" class="form-text"></div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                            <i class="bi bi-person-plus me-1"></i>Crear cuenta
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-3 small text-muted">
                ¿Ya tienes cuenta?
                <a href="<?= BASE_URL ?>/?action=login" class="text-decoration-none fw-semibold">
                    Inicia sesión
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
<script src="<?= BASE_URL ?>/../assets/js/main.js"></script>
<script>
    // Mostrar/ocultar contraseña
    document.getElementById('togglePass1').addEventListener('click', function () {
        const input = document.getElementById('contraseña');
        const icon  = document.getElementById('togglePass1Icon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });

    // Comprobar que las contraseñas coinciden
    const pass1  = document.getElementById('contraseña');
    const pass2  = document.getElementById('contraseña2');
    const msgDiv = document.getElementById('passMatchMsg');

    function checkPasswords() {
        if (pass2.value === '') { msgDiv.textContent = ''; return; }
        if (pass1.value === pass2.value) {
            msgDiv.textContent = '✔ Las contraseñas coinciden';
            msgDiv.className = 'form-text text-success';
        } else {
            msgDiv.textContent = '✘ Las contraseñas no coinciden';
            msgDiv.className = 'form-text text-danger';
        }
    }

    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);
</script>
</body>
</html>