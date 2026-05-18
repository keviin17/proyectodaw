<?php
// views/user/orders.php
// Requiere: $pedidos (array historial) o $pedido + $lineas (detalle de uno)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Mis Pedidos';
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag text-warning me-2"></i>Mis Pedidos</h2>

    <?php if (!empty($pedido) && !empty($lineas)): ?>
        <!-- Vista detalle de un pedido -->
        <div class="mb-3">
            <a href="<?= BASE_URL ?>/?action=mis_pedidos" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver a mis pedidos
            </a>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Pedido #<?= $pedido['id'] ?></span>
                    <?php
                    $badges = [
                        'pendiente'  => 'warning',
                        'procesando' => 'info',
                        'enviado'    => 'primary',
                        'entregado'  => 'success',
                        'cancelado'  => 'danger',
                    ];
                    $color = $badges[$pedido['estado']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $color ?> fs-6"><?= ucfirst($pedido['estado']) ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
                        <p class="mb-1"><strong>Método de pago:</strong> <?= htmlspecialchars($pedido['metodo_pago']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Dirección de envío:</strong> <?= htmlspecialchars($pedido['direccion_envio']) ?></p>
                        <?php if ($pedido['notas']): ?>
                            <p class="mb-1"><strong>Notas:</strong> <?= htmlspecialchars($pedido['notas']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Talla</th>
                                <th>Cantidad</th>
                                <th>Precio/ud.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lineas as $l): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($l['imagen'] ?? 'default.jpg') ?>"
                                                 width="40" height="40" style="object-fit:cover; border-radius:4px;">
                                            <?= htmlspecialchars($l['nombre']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($l['talla'] ?: '—') ?></td>
                                    <td><?= $l['cantidad'] ?></td>
                                    <td><?= number_format($l['precio_unitario'], 2) ?> €</td>
                                    <td class="fw-bold"><?= number_format($l['cantidad'] * $l['precio_unitario'], 2) ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">TOTAL</td>
                                <td class="fw-bold text-primary fs-5"><?= number_format($pedido['total'], 2) ?> €</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif (!empty($pedidos)): ?>
        <!-- Lista de pedidos -->
        <?php foreach ($pedidos as $ped): ?>
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
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-muted small">Pedido</div>
                            <div class="fw-bold">#<?= $ped['id'] ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Fecha</div>
                            <div><?= date('d/m/Y', strtotime($ped['fecha'])) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Total</div>
                            <div class="fw-bold text-primary"><?= number_format($ped['total'], 2) ?> €</div>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-<?= $color ?> fs-6"><?= ucfirst($ped['estado']) ?></span>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="<?= BASE_URL ?>/?action=pedido_detalle&id=<?= $ped['id'] ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Detalle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-bag-x display-1 text-muted d-block mb-3"></i>
            <h4 class="text-muted">Aún no has realizado ningún pedido</h4>
            <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-primary mt-2">
                <i class="bi bi-shop me-1"></i>Ir al catálogo
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>