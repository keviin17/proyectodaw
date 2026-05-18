<?php
// views/shop/product.php
// Requiere: $producto (array), $valoraciones (array), $media (float), $enDeseos (bool)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';

$producto     = $producto     ?? [];
$valoraciones = $valoraciones ?? [];
$media        = $media        ?? 0.0;
$enDeseos     = $enDeseos     ?? false;

$pageTitle = htmlspecialchars($producto['nombre'] ?? 'Producto');
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">

    <!-- Miga de pan -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb velora-breadcrumb px-3 py-2 rounded-3">
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>/" class="text-decoration-none">
                    <i class="bi bi-house-fill me-1"></i>Inicio
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>/?action=catalogo" class="text-decoration-none">
                    <i class="bi bi-grid me-1"></i>Catálogo
                </a>
            </li>
            <?php if (!empty($producto['categoria_nombre'])): ?>
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>/?action=catalogo&genero=<?= urlencode($producto['genero'] ?? '') ?>" class="text-decoration-none">
                    <?= htmlspecialchars($producto['categoria_nombre']) ?>
                </a>
            </li>
            <?php endif; ?>
            <li class="breadcrumb-item active fw-semibold" aria-current="page">
                <?= htmlspecialchars($producto['nombre']) ?>
            </li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Imagen del producto -->
        <div class="col-md-5 text-center">
            <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($producto['imagen'] ?? 'default.jpg') ?>"
                 class="img-fluid rounded shadow"
                 alt="<?= htmlspecialchars($producto['nombre']) ?>"
                 style="max-height:420px; object-fit:cover; width:100%;">
        </div>

        <!-- Información del producto -->
        <div class="col-md-7">
            <span class="badge bg-secondary mb-2"><?= htmlspecialchars($producto['categoria_nombre']) ?></span>
            <h1 class="h2 fw-bold mb-2"><?= htmlspecialchars($producto['nombre']) ?></h1>

            <!-- Valoración media -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star<?= $i <= round($media) ? '-fill' : '' ?> text-warning"></i>
                <?php endfor; ?>
                <span class="text-muted small">(<?= $media ?> / 5 — <?= count($valoraciones) ?> reseña<?= count($valoraciones) != 1 ? 's' : '' ?>)</span>
            </div>

            <!-- Precio -->
            <div class="mb-3">
                <?php if (!empty($producto['precio_oferta'])): ?>
                    <span class="text-decoration-line-through text-muted fs-5 me-2"><?= number_format($producto['precio'], 2) ?> €</span>
                    <span class="fs-2 fw-bold text-danger"><?= number_format($producto['precio_oferta'], 2) ?> €</span>
                    <span class="badge bg-danger ms-2">Oferta</span>
                <?php else: ?>
                    <span class="fs-2 fw-bold text-primary"><?= number_format($producto['precio'], 2) ?> €</span>
                <?php endif; ?>
            </div>

            <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($producto['descripcion'] ?? '')) ?></p>

            <!-- Stock -->
            <p class="mb-3">
                <strong>Disponibilidad:</strong>
                <?php if ($producto['stock'] > 10): ?>
                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>En stock (<?= $producto['stock'] ?> uds.)</span>
                <?php elseif ($producto['stock'] > 0): ?>
                    <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Últimas unidades (<?= $producto['stock'] ?> disponibles)</span>
                <?php else: ?>
                    <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Sin stock</span>
                <?php endif; ?>
            </p>

            <?php if ($producto['stock'] > 0): ?>
                <?php
                // Obtener tallas del propio producto
                $tallasProducto = [];
                if (!empty($producto['talla'])) {
                    $tallasProducto = array_filter(array_map('trim', explode(',', $producto['talla'])));
                }
                $tieneTallas = !empty($tallasProducto);
                ?>
                <!-- Formulario añadir al carrito -->
                <form action="<?= BASE_URL ?>/?action=carrito_anadir" method="POST" class="mb-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                    <div class="row g-2 align-items-end">
                        <?php if ($tieneTallas): ?>
                        <div class="col-4">
                            <label class="form-label fw-semibold small">Talla</label>
                            <select name="talla" class="form-select form-select-sm">
                                <?php foreach ($tallasProducto as $t): ?>
                                    <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="talla" value="">
                        <?php endif; ?>
                        <div class="col-<?= $tieneTallas ? '3' : '7' ?>">
                            <label class="form-label fw-semibold small">Cantidad</label>
                            <input type="number" name="cantidad" value="1"
                                   min="1" max="<?= $producto['stock'] ?>"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cart-plus me-1"></i>Añadir al carrito
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Lista de deseos — botón AJAX (no redirige) -->
            <div class="d-flex gap-2 mt-2">
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <button id="btnWishlistDetalle"
                            class="btn btn-sm <?= $enDeseos ? 'btn-outline-danger' : 'btn-outline-secondary' ?>"
                            data-id="<?= $producto['id'] ?>"
                            data-en-deseos="<?= $enDeseos ? '1' : '0' ?>">
                        <i class="bi <?= $enDeseos ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i>
                        <span id="textoWishlist"><?= $enDeseos ? 'En tu lista de deseos' : 'Añadir a deseos' ?></span>
                    </button>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver al catálogo
                </a>
            </div>
            <div id="msgWishlist" class="mt-2 small text-success d-none"></div>
        </div>
    </div>

    <!-- Sección de valoraciones -->
    <hr class="my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h3 class="h4 fw-bold mb-4"><i class="bi bi-chat-square-text me-2 text-warning"></i>Reseñas de clientes</h3>

            <?php if (!empty($valoraciones)): ?>
                <?php foreach ($valoraciones as $v): ?>
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="fw-semibold"><?= htmlspecialchars($v['usuario_nombre']) ?></span>
                                    <div class="mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= $v['puntuacion'] ? '-fill' : '' ?> text-warning small"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($v['fecha'])) ?></small>
                            </div>
                            <?php if ($v['comentario']): ?>
                                <p class="mt-2 mb-0 text-muted"><?= htmlspecialchars($v['comentario']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Aún no hay reseñas para este producto. ¡Sé el primero!</p>
            <?php endif; ?>
        </div>

        <!-- Formulario de valoración -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-star-fill text-warning me-1"></i>Escribe tu reseña
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['usuario_id'])): ?>
                        <form action="<?= BASE_URL ?>/?action=valorar" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Puntuación *</label>
                                <div class="d-flex gap-1" id="star-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <label class="fs-4 text-warning" style="cursor:pointer;" title="<?= $i ?> estrellas">
                                            <input type="radio" name="puntuacion" value="<?= $i ?>"
                                                   class="d-none" required>
                                            <i class="bi bi-star" id="star-<?= $i ?>"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Comentario</label>
                                <textarea name="comentario" class="form-control form-control-sm"
                                          rows="3" placeholder="¿Qué te pareció el producto?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning text-dark w-100 fw-bold btn-sm">
                                <i class="bi bi-send me-1"></i>Enviar valoración
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted small text-center py-2">
                            <a href="<?= BASE_URL ?>/?action=login">Inicia sesión</a> para dejar tu valoración.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ── Estrellas valoración ──
document.querySelectorAll('#star-rating label').forEach((label, idx) => {
    const input = label.querySelector('input');
    label.addEventListener('mouseenter', () => highlightStars(idx + 1));
    label.addEventListener('mouseleave', () => {
        const checked = document.querySelector('#star-rating input:checked');
        highlightStars(checked ? parseInt(checked.value) : 0);
    });
    input.addEventListener('change', () => highlightStars(idx + 1));
});
function highlightStars(n) {
    document.querySelectorAll('#star-rating label i').forEach((icon, i) => {
        icon.className = i < n ? 'bi bi-star-fill' : 'bi bi-star';
    });
}

// ── Botón wishlist AJAX (página de detalle) ──
(function () {
    const btn = document.getElementById('btnWishlistDetalle');
    if (!btn) return;
    const BASE_URL = '<?= BASE_URL ?>';

    btn.addEventListener('click', function () {
        const idProducto = btn.dataset.id;

        fetch(BASE_URL + '/?action=wishlist_toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_producto=' + encodeURIComponent(idProducto)
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.error === 'no_login') {
                window.location.href = BASE_URL + '/?action=login';
                return;
            }
            const icon   = btn.querySelector('i');
            const texto  = document.getElementById('textoWishlist');
            const msg    = document.getElementById('msgWishlist');

            if (data.status === 'added') {
                icon.className = 'bi bi-heart-fill me-1';
                btn.className  = 'btn btn-sm btn-outline-danger';
                texto.textContent = 'En tu lista de deseos';
                msg.textContent   = '❤️ Añadido a tu lista de deseos';
            } else {
                icon.className = 'bi bi-heart me-1';
                btn.className  = 'btn btn-sm btn-outline-secondary';
                texto.textContent = 'Añadir a deseos';
                msg.textContent   = 'Eliminado de tu lista de deseos';
            }
            msg.classList.remove('d-none');
            setTimeout(function () { msg.classList.add('d-none'); }, 2500);
        })
        .catch(function (err) {
            console.error('Wishlist error:', err);
        });
    });
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
