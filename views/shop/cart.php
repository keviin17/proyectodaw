<?php
// views/shop/cart.php
// Requiere: $items (array carrito), $total (float)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';

// Valores por defecto para evitar undefined variable
$items = $items ?? [];
$total = $total ?? array_sum(array_column($items, 'subtotal'));

$pageTitle = 'Mi Carrito';
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-cart3 text-warning me-2"></i>Mi Carrito</h2>

    <?php if (empty($items)): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted d-block mb-3"></i>
            <h4 class="text-muted">Tu carrito está vacío</h4>
            <p class="text-muted">Explora nuestro catálogo y añade productos que te gusten.</p>
            <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-primary mt-2">
                <i class="bi bi-shop me-1"></i>Ver catálogo
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Tabla de productos -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Talla</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($item['imagen'] ?? 'default.jpg') ?>"
                                                         width="60" height="60"
                                                         style="object-fit:cover; border-radius:8px;">
                                                    <a href="<?= BASE_URL ?>/?action=producto&id=<?= $item['id_producto'] ?>"
                                                       class="text-decoration-none fw-semibold text-dark">
                                                        <?= htmlspecialchars($item['nombre']) ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                    $talla = trim($item['talla'] ?? '');
                                                    $tallaTexto = ($talla !== '' && $talla !== '-') ? strtoupper($talla) : 'Sin talla';
                                                    $tallaBadge = ($talla !== '' && $talla !== '-') ? 'bg-dark text-white' : 'bg-light text-muted border';
                                                ?>
                                                <span class="badge <?= $tallaBadge ?> px-2 py-1">
                                                    <?= htmlspecialchars($tallaTexto) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($item['precio'], 2) ?> €</td>
                                            <td>
                                                <!-- Actualizar cantidad -->
                                                <form action="<?= BASE_URL ?>/?action=carrito_actualizar"
                                                      method="POST" class="d-flex align-items-center gap-1">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                    <input type="hidden" name="id_carrito" value="<?= $item['id'] ?>">
                                                    <input type="number" name="cantidad"
                                                           value="<?= $item['cantidad'] ?>"
                                                           min="1" max="<?= $item['stock'] ?>"
                                                           class="form-control form-control-sm"
                                                           style="width:65px;"
                                                           onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                <?= number_format($item['subtotal'], 2) ?> €
                                            </td>
                                            <td>
                                                <form action="<?= BASE_URL ?>/?action=carrito_eliminar" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                    <input type="hidden" name="id_carrito" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('¿Eliminar este artículo?')"
                                                            title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Seguir comprando
                    </a>
                </div>
            </div>

            <!-- Resumen del pedido -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-receipt me-1"></i>Resumen del pedido
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Subtotal</span>
                            <span><?= number_format($total, 2) ?> €</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Envío</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                            <span>Total</span>
                            <span class="text-primary"><?= number_format($total, 2) ?> €</span>
                        </div>
                        <a href="<?= BASE_URL ?>/?action=checkout" class="btn btn-warning text-dark w-100 fw-bold">
                            <i class="bi bi-lock me-1"></i>Proceder al pago
                        </a>
                        <p class="text-muted small text-center mt-2 mb-0">
                            <i class="bi bi-shield-check me-1 text-success"></i>Pago 100% seguro
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>