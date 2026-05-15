/**
 * VELORA SHOP — cart.js
 * Lógica del carrito: actualización de cantidad con fetch (AJAX)
 * y actualización del badge del navbar sin recargar.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Actualizar cantidad de una línea del carrito ────────────
    // Escucha el evento 'change' en los <input type="number"> del carrito
    document.querySelectorAll('.cart-qty-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const form = this.closest('form');
            if (!form) return;

            const idCarrito = form.querySelector('[name="id_carrito"]').value;
            const cantidad = parseInt(this.value, 10);
            const baseUrl = document.querySelector('meta[name="base-url"]')
                ? document.querySelector('meta[name="base-url"]').content
                : '/velora_shop/public';

            fetch(baseUrl + '/?action=carrito_actualizar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_carrito=' + encodeURIComponent(idCarrito) +
                    '&cantidad=' + encodeURIComponent(cantidad)
            })
                .then(function (res) {
                    if (res.ok) {
                        // Recargar la página para que PHP recalcule los subtotales
                        window.location.reload();
                    }
                })
                .catch(function (err) {
                    console.error('Error actualizando carrito:', err);
                });
        });
    });

    // ── Actualizar badge del navbar al añadir al carrito ───────
    // Escucha los formularios de "añadir al carrito" del catálogo
    document.querySelectorAll('form.add-to-cart-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const baseUrl = document.querySelector('meta[name="base-url"]')
                ? document.querySelector('meta[name="base-url"]').content
                : '/velora_shop/public';

            const formData = new FormData(form);

            fetch(baseUrl + '/?action=carrito_anadir', {
                method: 'POST',
                body: formData
            })
                .then(function (res) {
                    if (res.redirected) {
                        // Actualizar badge sin recargar página completa
                        actualizarBadgeCarrito(baseUrl);
                        mostrarToast('Producto añadido al carrito ✔');
                    }
                })
                .catch(function (err) {
                    console.error('Error añadiendo al carrito:', err);
                });
        });
    });

    /**
     * Pide el número de ítems del carrito al servidor
     * y actualiza el badge del icono de carrito en el navbar.
     */
    function actualizarBadgeCarrito(baseUrl) {
        fetch(baseUrl + '/?action=carrito_count')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                const badge = document.querySelector('.cart-badge');
                if (badge) {
                    badge.textContent = data.count || 0;
                    badge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            })
            .catch(function () { /* silencio */ });
    }

    /**
     * Muestra un toast simple de confirmación.
     */
    function mostrarToast(mensaje) {
        // Buscar contenedor de toasts o crearlo
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;';
            document.body.appendChild(container);
        }

        const toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-bg-success border-0 show';
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${mensaje}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>`;
        container.appendChild(toastEl);

        // Auto-eliminar tras 3 segundos
        setTimeout(function () {
            toastEl.remove();
        }, 3000);
    }

});