<?php
// views/shop/wishlist.php
// Requiere: $productos (array de productos de la lista de deseos)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = 'Lista de Deseos';
require __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-heart-fill text-danger me-2"></i>Mi Lista de Deseos</h2>

    <?php if (empty($productos)): ?>
        <div class="text-center py-5">
            <i class="bi bi-heart display-1 text-muted d-block mb-3"></i>
            <h4 class="text-muted">Tu lista de deseos está vacía</h4>
            <p class="text-muted">Guarda los productos que más te gusten para encontrarlos fácilmente más tarde.</p>
            <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-primary mt-2">
                <i class="bi bi-shop me-1"></i>Explorar catálogo
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($productos as $p): ?>
                <div class="col" id="wish-card-<?= $p['id_producto'] ?>">
                    <div class="card h-100 shadow-sm product-card">
                        <div class="position-relative">
                            <img src="<?= BASE_URL ?>/../assets/img/products/<?= htmlspecialchars($p['imagen'] ?? 'default.jpg') ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($p['nombre']) ?>"
                                 style="height:220px; object-fit:cover;">
                            <!-- Botón eliminar de deseos (AJAX — sin redirigir) -->
                            <button type="button"
                                    class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 btn-wish-remove"
                                    data-id="<?= $p['id_producto'] ?>"
                                    title="Quitar de deseos">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h6>
                            <div class="mt-auto">
                                <!-- Precio -->
                                <?php if (!empty($p['precio_oferta'])): ?>
                                    <div class="mb-2">
                                        <span class="text-decoration-line-through text-muted small me-1">
                                            <?= number_format($p['precio'], 2) ?> €
                                        </span>
                                        <span class="fw-bold text-danger">
                                            <?= number_format($p['precio_oferta'], 2) ?> €
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="fw-bold text-primary mb-2">
                                        <?= number_format($p['precio'], 2) ?> €
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>/?action=producto&id=<?= $p['id_producto'] ?>"
                                       class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                    <!-- Botón carrito: abre modal de talla -->
                                    <button type="button"
                                            class="btn btn-sm btn-primary btn-wish-cart"
                                            data-id="<?= $p['id_producto'] ?>"
                                            data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                            title="Añadir al carrito">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════════════════════════════
     MODAL: Elegir talla antes de añadir al carrito
════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalTalla" tabindex="-1" aria-labelledby="modalTallaLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTallaLabel">Elige tu talla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3" id="modalProductoNombre"></p>
                <div class="d-flex flex-wrap gap-2 justify-content-center" id="tallasContainer">
                    <?php
                    $tallasDisponibles = ['XS','S','M','L','XL','XXL'];
                    foreach ($tallasDisponibles as $t): ?>
                        <button type="button"
                                class="btn btn-outline-secondary btn-talla"
                                data-talla="<?= $t ?>"><?= $t ?></button>
                    <?php endforeach; ?>
                </div>
                <p class="text-danger small mt-2 d-none" id="tallaError">Por favor selecciona una talla.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnConfirmarTalla">
                    <i class="bi bi-cart-plus me-1"></i>Añadir al carrito
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const BASE_URL = '<?= BASE_URL ?>';

    /* ── 1. BOTÓN CARRITO desde wishlist — abre modal de talla ── */
    let productoIdPendiente = null;
    let tallaSeleccionada   = null;

    document.querySelectorAll('.btn-wish-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            productoIdPendiente = btn.dataset.id;
            tallaSeleccionada   = null;

            // Resetear selección visual
            document.querySelectorAll('.btn-talla').forEach(function (b) {
                b.classList.remove('active', 'btn-dark');
                b.classList.add('btn-outline-secondary');
            });
            document.getElementById('tallaError').classList.add('d-none');
            document.getElementById('modalProductoNombre').textContent = btn.dataset.nombre;

            const modal = new bootstrap.Modal(document.getElementById('modalTalla'));
            modal.show();
        });
    });

    // Seleccionar talla
    document.querySelectorAll('.btn-talla').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.btn-talla').forEach(function (b) {
                b.classList.remove('active', 'btn-dark');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-dark', 'active');
            tallaSeleccionada = btn.dataset.talla;
            document.getElementById('tallaError').classList.add('d-none');
        });
    });

    // Confirmar talla y enviar al carrito
    document.getElementById('btnConfirmarTalla').addEventListener('click', function () {
        if (!tallaSeleccionada) {
            document.getElementById('tallaError').classList.remove('d-none');
            return;
        }
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = BASE_URL + '/?action=carrito_anadir';
        const campos = { id_producto: productoIdPendiente, cantidad: '1', talla: tallaSeleccionada };
        Object.entries(campos).forEach(function ([k, v]) {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = k; input.value = v;
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
    });

    /* ── 2. BOTÓN ELIMINAR de la wishlist — AJAX (sin redirigir) ── */
    document.querySelectorAll('.btn-wish-remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const idProducto = btn.dataset.id;

            fetch(BASE_URL + '/?action=wishlist_toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_producto=' + encodeURIComponent(idProducto)
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.status === 'removed') {
                    // Quitar la tarjeta del DOM con animación suave
                    const card = document.getElementById('wish-card-' + idProducto);
                    if (card) {
                        card.style.transition = 'opacity 0.3s';
                        card.style.opacity = '0';
                        setTimeout(function () {
                            card.remove();
                            // Si no quedan tarjetas, mostrar mensaje vacío
                            const grid = document.querySelector('.row.row-cols-1');
                            if (grid && grid.children.length === 0) {
                                grid.closest('.container').innerHTML =
                                    '<div class="text-center py-5">'
                                    + '<i class="bi bi-heart display-1 text-muted d-block mb-3"></i>'
                                    + '<h4 class="text-muted">Tu lista de deseos está vacía</h4>'
                                    + '<p class="text-muted">Guarda los productos que más te gusten.</p>'
                                    + '<a href="' + BASE_URL + '/?action=catalogo" class="btn btn-primary mt-2">'
                                    + '<i class="bi bi-shop me-1"></i>Explorar catálogo</a>'
                                    + '</div>';
                            }
                        }, 350);
                    }
                }
            })
            .catch(function (err) {
                console.error('Wishlist remove error:', err);
            });
        });
    });

})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
