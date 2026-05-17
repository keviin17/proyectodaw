<?php
// views/admin/orders.php
// Requiere: $pedidos (array con datos del pedido + cliente)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Gestión de Pedidos';
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
                   class="list-group-item list-group-item-action active">
                    <i class="bi bi-bag-check me-2"></i>Pedidos
                </a>
                <a href="<?= BASE_URL ?>/?action=admin_usuarios"
                   class="list-group-item list-group-item-action">
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
                <i class="bi bi-bag-check text-warning me-2"></i>Gestión de Pedidos
            </h2>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>Total</th>
                                    <th>Método pago</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pedidos)): ?>
                                    <?php foreach ($pedidos as $ped): ?>
                                        <tr>
                                            <td class="fw-bold">#<?= $ped['id'] ?></td>
                                            <td><?= htmlspecialchars($ped['cliente_nombre']) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($ped['cliente_email']) ?></td>
                                            <td class="fw-bold text-success"><?= number_format($ped['total'], 2) ?> €</td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($ped['metodo_pago']) ?></span></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'pendiente'  => 'warning',
                                                    'procesando' => 'info',
                                                    'enviado'    => 'primary',
                                                    'entregado'  => 'success',
                                                    'cancelado'  => 'danger',
                                                ];
                                                $color = $badges[$ped['estado']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= ucfirst($ped['estado']) ?></span>
                                            </td>
                                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($ped['fecha'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEstado"
                                                        onclick="prepararCambioEstado(<?= $ped['id'] ?>, '<?= $ped['estado'] ?>')"
                                                        title="Cambiar estado">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalDetalle"
                                                        onclick="verDetalle(<?= $ped['id'] ?>)"
                                                        title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>No hay pedidos todavía.
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

<!-- Modal cambiar estado -->
<div class="modal fade" id="modalEstado" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title"><i class="bi bi-arrow-repeat me-1"></i>Cambiar estado</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/?action=admin_cambiar_estado" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-body">
                    <input type="hidden" name="id_pedido" id="estado_id_pedido">
                    <label class="form-label fw-semibold">Nuevo estado</label>
                    <select name="estado" id="estado_select" class="form-select">
                        <option value="pendiente">Pendiente</option>
                        <option value="procesando">Procesando</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregado">Entregado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-dark btn-sm fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal detalle de pedido (carga vía AJAX o incluye los datos ya disponibles) -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title"><i class="bi bi-receipt me-1"></i>Detalle del pedido</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detallePedidoBody">
                <p class="text-center text-muted">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<script>
function prepararCambioEstado(idPedido, estadoActual) {
    document.getElementById('estado_id_pedido').value = idPedido;
    document.getElementById('estado_select').value = estadoActual;
}

function verDetalle(idPedido) {
    const body = document.getElementById('detallePedidoBody');
    body.innerHTML = '<p class="text-center py-3"><div class="spinner-border text-warning" role="status"></div></p>';
    fetch('<?= BASE_URL ?>/?action=pedido_detalle&id=' + idPedido + '&formato=json')
        .then(r => r.json())
        .then(data => {
            if (!data || data.error) {
                body.innerHTML = '<p class="text-danger text-center">No se pudo cargar el detalle.</p>';
                return;
            }
            // A1 — Función de escape para prevenir XSS
            function esc(str) {
                const d = document.createElement('div');
                d.textContent = str ?? '';
                return d.innerHTML;
            }
            let html = `<p><strong>Cliente:</strong> ${esc(data.pedido.cliente_nombre)} &mdash; ${esc(data.pedido.cliente_email)}</p>
                        <p><strong>Dirección envío:</strong> ${esc(data.pedido.direccion_envio)}</p>
                        <p><strong>Notas:</strong> ${esc(data.pedido.notas) || '—'}</p>
                        <table class="table table-sm">
                            <thead><tr><th>Producto</th><th>Talla</th><th>Cant.</th><th>Precio/ud.</th><th>Subtotal</th></tr></thead>
                            <tbody>`;
            data.lineas.forEach(l => {
                html += `<tr>
                    <td>${esc(l.nombre)}</td>
                    <td>${esc(l.talla) || '—'}</td>
                    <td>${parseInt(l.cantidad)}</td>
                    <td>${parseFloat(l.precio_unitario).toFixed(2)} €</td>
                    <td><strong>${(l.cantidad * l.precio_unitario).toFixed(2)} €</strong></td>
                </tr>`;
            });
            html += `</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">TOTAL</td>
                     <td class="fw-bold text-success">${parseFloat(data.pedido.total).toFixed(2)} €</td></tr>
                     </tfoot></table>`;
            body.innerHTML = html;
        })
        .catch(() => {
            body.innerHTML = '<p class="text-danger text-center">Error al conectar con el servidor.</p>';
        });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>