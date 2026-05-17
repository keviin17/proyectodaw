<?php
// views/user/profile.php
// Requiere: $usuario (array con datos del usuario)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Mi Perfil';
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-person-circle text-warning me-2"></i>Mi Perfil</h2>

    <div class="row g-4">
        <!-- Formulario de datos personales -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-pencil me-1"></i>Datos personales
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/?action=perfil_guardar" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre completo *</label>
                            <input type="text" name="nombre" class="form-control"
                                   value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                                   required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control"
                                   value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                                   disabled>
                            <div class="form-text">El email no puede modificarse.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control"
                                   value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                                   placeholder="+34 600 000 000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dirección</label>
                            <input type="text" name="direccion" class="form-control"
                                   value="<?= htmlspecialchars($usuario['direccion'] ?? '') ?>"
                                   placeholder="Calle y número">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control"
                                       value="<?= htmlspecialchars($usuario['ciudad'] ?? '') ?>">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Código postal</label>
                                <input type="text" name="codigo_postal" class="form-control"
                                       value="<?= htmlspecialchars($usuario['codigo_postal'] ?? '') ?>"
                                       maxlength="10">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning text-dark fw-bold">
                            <i class="bi bi-save me-1"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral: resumen de cuenta y cambio de contraseña -->
        <div class="col-lg-5">
            <!-- Resumen de cuenta -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-person me-1"></i>Resumen de cuenta
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Rol</span>
                        <span>
                            <?php if (($usuario['rol'] ?? '') === 'admin'): ?>
                                <span class="badge bg-danger"><i class="bi bi-shield-fill me-1"></i>Administrador</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Cliente</span>
                            <?php endif; ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Miembro desde</span>
                        <span><?= date('d/m/Y', strtotime($usuario['fecha_registro'] ?? 'now')) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-success">Activo</span>
                    </li>
                </ul>
                <div class="card-footer bg-white">
                    <a href="<?= BASE_URL ?>/?action=mis_pedidos" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-bag me-1"></i>Ver mis pedidos
                    </a>
                </div>
            </div>

            <!-- Cambiar contraseña -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-key me-1"></i>Cambiar contraseña
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/?action=cambiar_contrasena" method="POST"
                          id="formCambioPass" onsubmit="return validarPass()">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Contraseña actual *</label>
                            <input type="password" name="pass_actual" class="form-control form-control-sm"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nueva contraseña *</label>
                            <input type="password" name="pass_nueva" id="pass_nueva"
                                   class="form-control form-control-sm"
                                   required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Repetir nueva contraseña *</label>
                            <input type="password" name="pass_nueva2" id="pass_nueva2"
                                   class="form-control form-control-sm"
                                   required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-outline-dark btn-sm w-100 fw-bold">
                            <i class="bi bi-lock me-1"></i>Cambiar contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validarPass() {
    const p1 = document.getElementById('pass_nueva').value;
    const p2 = document.getElementById('pass_nueva2').value;
    if (p1 !== p2) {
        alert('Las contraseñas no coinciden.');
        return false;
    }
    return true;
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>