<?php
// views/shop/index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/constants.php';
$pageTitle = isset($generoActual) ? ucfirst($generoActual) : 'Catálogo';

// Obtener IDs de productos en la wishlist del usuario para marcar los botones
$wishlistIds = [];
if (!empty($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/../../models/Wishlist.php';
    $wm = new Wishlist();
    $wishlistItems = $wm->getByUsuario($_SESSION['usuario_id']);
    foreach ($wishlistItems as $wi) {
        $wishlistIds[] = (int)$wi['id_producto'];
    }
}

require __DIR__ . '/../layout/header.php';
?>

<!-- HERO BANNER -->
<section class="hero-banner text-white py-5 mb-4">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Nueva Temporada</h1>
        <p class="lead">Descubre los estilos más actuales en Velora Shop</p>
        <a href="#catalogo" class="btn btn-primary btn-lg">Ver Colección</a>
    </div>
</section>

<!-- FILTROS DE CATEGORIA -->
<div class="container" id="catalogo">
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h2 class="mb-0">Nuestros Productos</h2>
        </div>
        <div class="col-md-6">
            <form action="<?= BASE_URL ?>/" method="GET" class="d-flex gap-2">
                <input type="hidden" name="action" value="buscar">
                <input type="text" name="q" class="form-control"
                    placeholder="Buscar productos..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
            </form>
        </div>
    </div>

    <!-- FILTROS POR GENERO -->
    <div class="mb-4">
        <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-sm btn-outline-secondary me-1">Todos</a>
        <a href="<?= BASE_URL ?>/?action=catalogo&amp;genero=hombre" class="btn btn-sm btn-outline-primary me-1">Hombre</a>
        <a href="<?= BASE_URL ?>/?action=catalogo&amp;genero=mujer" class="btn btn-sm btn-outline-danger me-1">Mujer</a>
        <a href="<?= BASE_URL ?>/?action=catalogo&amp;genero=ni%C3%B1o" class="btn btn-sm btn-outline-success">Niño</a>
    </div>

    <!-- GRID DE PRODUCTOS -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $p): ?>
                <?php $enDeseos = in_array((int)$p['id'], $wishlistIds); ?>
                <div class="col">
                    <div class="card h-100 shadow-sm product-card position-relative">
                        <?php
                        $tieneOferta = !empty($p['precio_oferta']) && $p['precio_oferta'] > 0;
                        $sinTalla = empty(trim($p['talla'] ?? ''));
                        $agotado = ($p['stock'] <= 0) || $sinTalla;
                        if ($agotado):
                        ?>
                            <span class="badge bg-secondary position-absolute top-0 end-0 m-2">
                                Agotado
                            </span>
                        <?php elseif ($tieneOferta): ?>
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                🏷️ Oferta
                            </span>
                        <?php elseif ($p['destacado']): ?>
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">
                                ⭐ Destacado
                            </span>
                        <?php endif; ?>
                        <img src="<?= BASE_URL ?>/../assets/img/products/<?= htmlspecialchars($p['imagen'] ?? 'default.jpg') ?>"
                            class="card-img-top"
                            alt="<?= htmlspecialchars($p['nombre']) ?>"
                            style="height:220px; object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= htmlspecialchars($p['nombre']) ?></h6>
                            <p class="text-muted small"><?= htmlspecialchars($p['categoria_nombre']) ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <?php if ($tieneOferta && !$agotado): ?>
                                    <div>
                                        <span class="text-muted text-decoration-line-through small">
                                            <?= number_format($p['precio'], 2) ?> &euro;
                                        </span>
                                        <span class="fw-bold text-danger fs-5 ms-1">
                                            <?= number_format($p['precio_oferta'], 2) ?> &euro;
                                        </span>
                                    </div>
                                <?php elseif ($agotado): ?>
                                    <span class="fw-bold text-muted fs-5">
                                        <?= number_format($p['precio'], 2) ?> &euro;
                                    </span>
                                <?php else: ?>
                                    <span class="fw-bold text-primary fs-5">
                                        <?= number_format($p['precio'], 2) ?> &euro;
                                    </span>
                                <?php endif; ?>
                                <div class="d-flex gap-1">
                                    <a href="<?= BASE_URL ?>/?action=producto&amp;id=<?= $p['id'] ?>"
                                        class="btn btn-sm btn-outline-primary">Ver</a>

                                    <!-- Botón + : abre modal para elegir talla (o muestra agotado) -->
                                    <button class="btn btn-sm <?= $agotado ? 'btn-secondary' : 'btn-primary' ?> btn-add-to-cart"
                                        data-id="<?= $p['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                        data-tallas="<?= htmlspecialchars($p['talla'] ?? '') ?>"
                                        data-stock="<?= (int)$p['stock'] ?>"
                                        title="<?= $agotado ? 'Agotado' : 'Añadir al carrito' ?>">+</button>

                                    <?php if (!empty($_SESSION['usuario_id'])): ?>
                                        <!-- Botón corazón AJAX — estado inicial desde BD -->
                                        <button class="btn btn-sm btn-wishlist <?= $enDeseos ? 'active' : '' ?>"
                                            data-id="<?= $p['id'] ?>"
                                            data-base="<?= BASE_URL ?>"
                                            title="Lista de deseos">
                                            <i class="bi <?= $enDeseos ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-search display-4 d-block mb-3"></i>
                    <h5>No se encontraron productos</h5>
                    <a href="<?= BASE_URL ?>/?action=catalogo" class="btn btn-outline-primary mt-2">Ver todos</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginacion -->
    <?php if (!empty($paginas) && $paginas > 1): ?>
        <nav class="d-flex justify-content-center mb-5">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $paginas; $i++): ?>
                    <li class="page-item <?= ($page ?? 1) == $i ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/?action=catalogo&pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
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
                    <!-- Las tallas se renderizan dinámicamente por JavaScript -->
                </div>
                <div class="text-center d-none" id="msgAgotado">
                    <i class="bi bi-x-circle text-danger fs-1 d-block mb-2"></i>
                    <p class="fw-semibold text-danger mb-1">Producto agotado</p>
                    <p class="text-muted small">Este producto no tiene stock disponible actualmente.</p>
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
    (function() {
        const BASE_URL = '<?= BASE_URL ?>';

        /* ── 1. WISHLIST AJAX ─────────────────────────────────────── */
        document.querySelectorAll('.btn-wishlist').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const idProducto = btn.dataset.id;

                fetch(BASE_URL + '/?action=wishlist_toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id_producto=' + encodeURIComponent(idProducto)
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data.error === 'no_login') {
                            window.location.href = BASE_URL + '/?action=login';
                            return;
                        }
                        const icon = btn.querySelector('i');
                        if (data.status === 'added') {
                            icon.className = 'bi bi-heart-fill text-danger';
                            btn.classList.add('active');
                            mostrarToastWish('❤️ Añadido a tu lista de deseos');
                        } else {
                            icon.className = 'bi bi-heart';
                            btn.classList.remove('active');
                            mostrarToastWish('Eliminado de tu lista de deseos');
                        }
                    })
                    .catch(function(err) {
                        console.error('Wishlist error:', err);
                    });
            });
        });

        function mostrarToastWish(msg) {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;';
                document.body.appendChild(container);
            }
            const t = document.createElement('div');
            t.className = 'toast align-items-center text-bg-danger border-0 show';
            t.setAttribute('role', 'alert');
            t.innerHTML = '<div class="d-flex"><div class="toast-body">' + msg + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
            container.appendChild(t);
            setTimeout(function() {
                t.remove();
            }, 2800);
        }

        /* ── 2. MODAL TALLAS (botón +) ───────────────────────────── */
        let productoIdPendiente = null;
        let tallaSeleccionada = null;

        document.querySelectorAll('.btn-add-to-cart').forEach(function(btn) {
            btn.addEventListener('click', function() {
                productoIdPendiente = btn.dataset.id;
                tallaSeleccionada = null;

                document.getElementById('tallaError').classList.add('d-none');
                document.getElementById('modalProductoNombre').textContent = btn.dataset.nombre;

                // Construir los botones de talla desde data-tallas del producto
                const tallasStr = btn.dataset.tallas || '';
                const tallasArr = tallasStr ? tallasStr.split(',').map(t => t.trim()).filter(t => t) : [];
                const container = document.getElementById('tallasContainer');
                container.innerHTML = '';

                if (tallasArr.length === 0) {
                    // Sin tallas definidas: mostrar modal indicando que está agotado
                    const msgAgotado = document.getElementById('msgAgotado');
                    const btnConfirmar = document.getElementById('btnConfirmarTalla');
                    if (msgAgotado) msgAgotado.classList.remove('d-none');
                    if (btnConfirmar) btnConfirmar.classList.add('d-none');
                } else {
                    const msgAgotado = document.getElementById('msgAgotado');
                    const btnConfirmar = document.getElementById('btnConfirmarTalla');
                    if (msgAgotado) msgAgotado.classList.add('d-none');
                    if (btnConfirmar) btnConfirmar.classList.remove('d-none');
                    tallasArr.forEach(function(t) {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'btn btn-outline-secondary btn-talla';
                        b.dataset.talla = t;
                        b.textContent = t;
                        b.addEventListener('click', function() {
                            container.querySelectorAll('.btn-talla').forEach(function(x) {
                                x.classList.remove('active', 'btn-dark');
                                x.classList.add('btn-outline-secondary');
                            });
                            b.classList.remove('btn-outline-secondary');
                            b.classList.add('btn-dark', 'active');
                            tallaSeleccionada = t;
                            document.getElementById('tallaError').classList.add('d-none');
                        });
                        container.appendChild(b);
                    });
                }

                // Abrir el modal
                const modal = new bootstrap.Modal(document.getElementById('modalTalla'));
                modal.show();
            });
        });

        // Confirmar: enviar al carrito
        document.getElementById('btnConfirmarTalla').addEventListener('click', function() {
            if (!tallaSeleccionada) {
                document.getElementById('tallaError').classList.remove('d-none');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = BASE_URL + '/?action=carrito_anadir';
            const campos = {
                id_producto: productoIdPendiente,
                cantidad: '1',
                talla: tallaSeleccionada
            };
            Object.entries(campos).forEach(function([k, v]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = k;
                input.value = v;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        });

    })();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>