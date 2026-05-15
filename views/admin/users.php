<?php
// views/admin/users.php
// Requiere: $usuarios (array con todos los usuarios)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Gestión de Usuarios';
require __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2">
            <div class="list-group list-group-flush sticky-top" style="top:80px;">
                <a href="<?= BASE_URL ?>/?action=admin_dashboard"
                    class="list-group-item list-group-item-action">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a href="<?= BASE_URL ?>/?action=admin_productos"
                    class="list-group-item list-group-item-action">
                    <i class="bi bi-box-seam me-2"></i>Productos
                </a>
                <a href="<?= BASE_URL ?>/?action=admin_pedidos"
                    class="list-group-item list-group-item-action">
                    <i class="bi bi-bag-check me-2"></i>Pedidos
                </a>
                <a href="<?= BASE_URL ?>/?action=admin_usuarios"
                    class="list-group-item list-group-item-action active">
                    <i class="bi bi-people me-2"></i>Usuarios
                </a>
                <hr>
                <a href="<?= BASE_URL ?>/" class="list-group-item list-group-item-action text-secondary">
                    <i class="bi bi-shop me-2"></i>Ver tienda
                </a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-md-10">
            <h2 class="fw-bold mb-4">
                <i class="bi bi-people text-warning me-2"></i>Gestión de Usuarios
            </h2>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Registrado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usuarios)): ?>
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td class="text-muted small">#<?= $u['id'] ?></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($u['nombre']) ?></td>
                                            <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                                            <td>
                                                <?php if ($u['rol'] === 'admin'): ?>
                                                    <span class="badge bg-danger"><i class="bi bi-shield-fill me-1"></i>Admin</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Cliente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted small"><?= date('d/m/Y', strtotime($u['fecha_registro'])) ?></td>
                                            <td>
                                                <?php if ($u['activo']): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                // No se puede desactivar a uno mismo
                                                $esMisma = ($u['id'] == $_SESSION['usuario_id']);
                                                ?>
                                                <?php if (!$esMisma): ?>
                                                    <!-- Activar/Desactivar -->
                                                    <a href="<?= BASE_URL ?>/?action=admin_toggle_usuario&id=<?= $u['id'] ?>"
                                                        class="btn btn-sm btn-outline-<?= $u['activo'] ? 'danger' : 'success' ?> me-1"
                                                        onclick="return confirm('<?= $u['activo'] ? '¿Desactivar esta cuenta?' : '¿Activar esta cuenta?' ?>')"
                                                        title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                                                        <i class="bi bi-<?= $u['activo'] ? 'person-x' : 'person-check' ?>"></i>
                                                    </a>
                                                    <!-- Cambiar Rol -->
                                                    <form action="<?= BASE_URL ?>/?action=admin_cambiar_rol" method="POST"
                                                        class="d-inline-flex align-items-center gap-1"
                                                        onsubmit="return confirm('¿Cambiar el rol de este usuario?')">
                                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                        <select name="rol" class="form-select form-select-sm" style="width:auto; min-width:90px;">
                                                            <option value="cliente" <?= $u['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                                            <option value="admin" <?= $u['rol'] === 'admin'   ? 'selected' : '' ?>>Admin</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Cambiar rol">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted small">(Tú)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>