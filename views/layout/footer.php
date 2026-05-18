<?php // views/layout/footer.php ?>
<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-bag-heart-fill text-warning me-1"></i>Velora Shop
                </h5>
                <p class="text-secondary small">
                    Tu tienda de moda casual favorita. Encuentra las últimas tendencias
                    para hombre, mujer y niño a precios accesibles.
                </p>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold mb-3"><i class="bi bi-fire text-warning me-1"></i>Tendencias</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=catalogo&genero=hombre"><i class="bi bi-arrow-right-short"></i>Hombre</a></li>
                    <li class="mb-1"><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=catalogo&genero=mujer"><i class="bi bi-arrow-right-short"></i>Mujer</a></li>
                    <li class="mb-1"><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=catalogo&genero=ni%C3%B1o"><i class="bi bi-arrow-right-short"></i>Niño</a></li>
                    <li class="mb-1"><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=catalogo"><i class="bi bi-arrow-right-short"></i>Novedades</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="fw-bold mb-3">Mi Cuenta</h6>
                <ul class="list-unstyled small">
                    <?php if (!empty($_SESSION['usuario_id'])): ?>
                        <li><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=perfil">Mi Perfil</a></li>
                        <li><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=mis_pedidos">Mis Pedidos</a></li>
                        <li><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=wishlist">Lista de Deseos</a></li>
                    <?php else: ?>
                        <li><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=login">Iniciar Sesión</a></li>
                        <li><a class="text-secondary text-decoration-none footer-link" href="<?= BASE_URL ?>/?action=register">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Contacto</h6>
                <p class="text-secondary small mb-1"><i class="bi bi-envelope me-2"></i>info@velorashop.com</p>
                <p class="text-secondary small mb-1"><i class="bi bi-telephone me-2"></i>+34 900 123 456</p>
                <p class="text-secondary small"><i class="bi bi-geo-alt me-2"></i>Madrid, España</p>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small class="text-secondary">
                    &copy; <?= date('Y') ?> Velora Shop &mdash; Proyecto Final DAW 2024/2025
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                <small class="text-secondary">Desarrollado con PHP &bull; MySQL &bull; Bootstrap 5</small>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts personalizados -->
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>

<!-- ═══════════════════════════════════════════════════════════════
     TEMA OSCURO / CLARO
     - Se guarda en localStorage('velora-tema') = 'dark' | 'light'
     - Se aplica en TODAS las páginas al cargar, sin parpadeo
     - El toggle solo cambia la preferencia y la clase en body
════════════════════════════════════════════════════════════════ -->
<script>
(function () {
    const btn  = document.getElementById('btnTema');
    const icon = document.getElementById('iconTema');
    if (!btn || !icon) return;

    /**
     * Aplica o quita la clase dark-mode en body y actualiza el icono.
     * No recarga la página ni redirige.
     */
    function aplicarTema(dark) {
        if (dark) {
            document.body.classList.add('dark-mode');
            icon.className = 'bi bi-sun-fill';
            btn.title = 'Cambiar a tema claro';
        } else {
            document.body.classList.remove('dark-mode');
            icon.className = 'bi bi-moon-fill';
            btn.title = 'Cambiar a tema oscuro';
        }
    }

    // Al cargar la página: leer preferencia guardada y aplicar
    var temaGuardado = localStorage.getItem('velora-tema');
    if (temaGuardado === 'dark') {
        aplicarTema(true);
    } else if (temaGuardado === 'light') {
        aplicarTema(false);
    } else {
        // Sin preferencia: respetar preferencia del sistema
        var prefiereDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        aplicarTema(prefiereDark);
    }

    // Toggle al pulsar el botón: guardar nueva preferencia
    btn.addEventListener('click', function () {
        var isDark = document.body.classList.toggle('dark-mode');
        aplicarTema(isDark);
        localStorage.setItem('velora-tema', isDark ? 'dark' : 'light');
    });
})();
</script>
</body>
</html>
