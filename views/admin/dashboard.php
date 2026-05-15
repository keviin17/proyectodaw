<?php
// views/admin/dashboard.php
// Requiere: $stats (array con contadores), sesión admin activa
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Panel de Administración';
require __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2">
            <div class="list-group list-group-flush sticky-top" style="top:80px;">
                <a href="<?= BASE_URL ?>/?action=admin_dashboard"
                   class="list-group-item list-group-item-action active">
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
                   class="list-group-item list-group-item-action">
                    <i class="bi bi-people me-2"></i>Usuarios
                </a>
                <hr>
                <a href="<?= BASE_URL ?>/" class="list-group-item list-group-item-action text-secondary">
                    <i class="bi bi-shop me-2"></i>Ver tienda
                </a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="col-md-10">
            <h2 class="mb-4 fw-bold">
                <i class="bi bi-speedometer2 text-warning me-2"></i>Dashboard
            </h2>

            <!-- Tarjetas de resumen -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people fs-3 text-primary"></i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold"><?= $stats['total_usuarios'] ?? 0 ?></div>
                                <div class="text-muted small">Usuarios registrados</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-box-seam fs-3 text-success"></i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold"><?= $stats['total_productos'] ?? 0 ?></div>
                                <div class="text-muted small">Productos activos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-bag-check fs-3 text-warning"></i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold"><?= $stats['total_pedidos'] ?? 0 ?></div>
                                <div class="text-muted small">Pedidos totales</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-currency-euro fs-3 text-danger"></i>
                            </div>
                            <div>
                                <div class="fs-2 fw-bold"><?= number_format($stats['ingresos_totales'] ?? 0, 2) ?> €</div>
                                <div class="text-muted small">Ingresos totales</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- Últimos pedidos -->
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold border-bottom">
                            <i class="bi bi-clock-history me-1 text-warning"></i>Últimos pedidos
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($ultimosPedidos)): ?>
                                            <?php foreach ($ultimosPedidos as $p): ?>
                                                <tr>
                                                    <td><a href="<?= BASE_URL ?>/?action=admin_pedidos">#<?= $p['id'] ?></a></td>
                                                    <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
                                                    <td class="fw-bold"><?= number_format($p['total'], 2) ?> €</td>
                                                    <td>
                                                        <?php
                                                        $badges = [
                                                            'pendiente'  => 'warning',
                                                            'procesando' => 'info',
                                                            'enviado'    => 'primary',
                                                            'entregado'  => 'success',
                                                            'cancelado'  => 'danger',
                                                        ];
                                                        $color = $badges[$p['estado']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($p['estado']) ?></span>
                                                    </td>
                                                    <td class="text-muted small"><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center text-muted py-3">Sin pedidos todavía</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-end">
                            <a href="<?= BASE_URL ?>/?action=admin_pedidos" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                    </div>
                </div>

                <!-- Productos con poco stock -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold border-bottom">
                            <i class="bi bi-exclamation-triangle me-1 text-danger"></i>Stock bajo (&lt; 10 uds.)
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($productosBajoStock)): ?>
                                    <?php foreach ($productosBajoStock as $prod): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="small"><?= htmlspecialchars($prod['nombre']) ?></span>
                                            <span class="badge bg-<?= $prod['stock'] <= 3 ? 'danger' : 'warning' ?> text-dark">
                                                <?= $prod['stock'] ?> uds.
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted text-center py-3">Todo el stock está bien</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="card-footer bg-white text-end">
                            <a href="<?= BASE_URL ?>/?action=admin_productos" class="btn btn-sm btn-outline-success">Gestionar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>