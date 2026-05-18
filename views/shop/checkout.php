<?php
// views/shop/checkout.php
// Requiere: $items (array carrito), $total (float)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';

// Valores por defecto para evitar undefined variable
$items = $items ?? [];
$total = $total ?? array_sum(array_column($items, 'subtotal'));

$pageTitle = 'Finalizar pedido';
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag-check text-warning me-2"></i>Finalizar pedido</h2>

    <div class="row g-4">
        <!-- Formulario de envío y pago -->
        <div class="col-lg-7">
            <form action="<?= BASE_URL ?>/?action=confirmar_pedido" method="POST" id="formCheckout">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <!-- Datos de envío -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-geo-alt me-1"></i>Dirección de envío
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dirección completa *</label>
                            <input type="text" name="direccion_envio" class="form-control"
                                   placeholder="Calle, número, piso..."
                                   value="<?= htmlspecialchars($_SESSION['usuario_direccion'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notas adicionales</label>
                            <textarea name="notas" class="form-control" rows="2"
                                      placeholder="Instrucciones especiales para la entrega (opcional)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Método de pago -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-credit-card me-1"></i>Método de pago
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio"
                                   name="metodo_pago" id="pago_tarjeta"
                                   value="tarjeta" checked>
                            <label class="form-check-label" for="pago_tarjeta">
                                <i class="bi bi-credit-card me-1 text-primary"></i>Tarjeta de crédito/débito
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio"
                                   name="metodo_pago" id="pago_paypal"
                                   value="paypal">
                            <label class="form-check-label" for="pago_paypal">
                                <i class="bi bi-paypal me-1 text-primary"></i>PayPal
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="metodo_pago" id="pago_transferencia"
                                   value="transferencia">
                            <label class="form-check-label" for="pago_transferencia">
                                <i class="bi bi-bank me-1 text-primary"></i>Transferencia bancaria
                            </label>
                        </div>
                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Nota:</strong> Este es un proyecto de demostración. No se realizará ningún cargo real.
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning text-dark w-100 fw-bold py-3"
                        onclick="return confirm('¿Confirmar el pedido?')">
                    <i class="bi bi-bag-check me-2"></i>Confirmar pedido — <?= number_format($total, 2) ?> €
                </button>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 sticky-top" style="top:80px;">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-receipt me-1"></i>Tu pedido
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($items as $item): ?>
                            <li class="list-group-item d-flex align-items-center gap-3 py-3">
                                <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($item['imagen'] ?? 'default.jpg') ?>"
                                     width="50" height="50"
                                     style="object-fit:cover; border-radius:6px;">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small"><?= htmlspecialchars($item['nombre']) ?></div>
                                    <div class="text-muted small">
                                        Talla: <?= htmlspecialchars($item['talla'] ?: '—') ?> &bull;
                                        Cant.: <?= $item['cantidad'] ?>
                                    </div>
                                </div>
                                <div class="fw-bold text-primary small"><?= number_format($item['subtotal'], 2) ?> €</div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="p-3">
                        <div class="d-flex justify-content-between text-muted small mb-1">
                            <span>Subtotal</span><span><?= number_format($total, 2) ?> €</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>Envío</span><span class="text-success">Gratis</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span class="text-primary fs-5"><?= number_format($total, 2) ?> €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>