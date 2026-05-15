/**
 * VELORA SHOP — main.js
 * Scripts generales: alertas, tooltips, validación de formularios
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-cerrar alertas después de 4 segundos ──────────────
    const alerts = document.querySelectorAll('.alert.alert-success, .alert.alert-danger');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // ── Activar tooltips de Bootstrap ─────────────────────────
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ── Activar popovers de Bootstrap ─────────────────────────
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    popoverTriggerList.forEach(function (el) {
        new bootstrap.Popover(el);
    });

    // ── Validación HTML5 con estilos Bootstrap ─────────────────
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // ── Confirmar antes de eliminar (botones con data-confirm) ──
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.getAttribute('data-confirm') || '¿Estás seguro?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // ── Previsualizar imagen al seleccionarla (formulario admin) ─
    const imgInput = document.getElementById('inputImagen');
    const imgPreview = document.getElementById('previewImagen');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                    imgPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ── Resaltar nav-link activo según la URL actual ────────────
    const currentAction = new URLSearchParams(window.location.search).get('action') || 'home';
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        const href = link.getAttribute('href') || '';
        if (href.includes('action=' + currentAction)) {
            link.classList.add('active');
        }
    });

});