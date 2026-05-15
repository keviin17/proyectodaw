<?php
// views/admin/products.php
// Requiere: $productos (array), $categorias (array), sesión admin activa
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/constants.php';

// Recuperar variables pasadas desde el controller
$categorias = $GLOBALS['categorias'] ?? [];
$productos  = $GLOBALS['productos']  ?? [];

$pageTitle = 'Gestión de Productos';
require __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2">
            <div class="list-group list-group-flush sticky-top" style="top:80px;">
                <a href="<?= BASE_URL ?>/?action=admin_dashboard"
                   class="list-group-item list-group-item-action">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a href="<?= BASE_URL ?>/?action=admin_productos"
                   class="list-group-item list-group-item-action active">
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

        <!-- Contenido -->
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-box-seam text-warning me-2"></i>Productos
                </h2>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <!-- Mini buscador -->
                    <form action="<?= BASE_URL ?>/?action=admin_productos" method="GET" class="d-flex gap-1">
                        <input type="hidden" name="action" value="admin_productos">
                        <input type="text" name="q" class="form-control form-control-sm"
                               placeholder="Buscar producto..."
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                               style="width:200px;">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if (!empty($_GET['q'])): ?>
                            <a href="<?= BASE_URL ?>/?action=admin_productos" class="btn btn-sm btn-outline-danger" title="Limpiar búsqueda">
                                <i class="bi bi-x"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                    <!-- Botón nuevo producto -->
                    <button class="btn btn-warning text-dark fw-bold"
                            data-bs-toggle="modal" data-bs-target="#modalProducto"
                            onclick="limpiarFormProducto()">
                        <i class="bi bi-plus-lg me-1"></i>Nuevo producto
                    </button>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Destacado</th>
                                    <th>En oferta</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($productos)): ?>
                                    <?php foreach ($productos as $p): ?>
                                        <tr>
                                            <td class="text-muted small">#<?= $p['id'] ?></td>
                                            <td>
                                                <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($p['imagen'] ?? 'default.jpg') ?>"
                                                     width="50" height="50"
                                                     style="object-fit:cover; border-radius:6px;">
                                            </td>
                                            <td class="fw-semibold"><?= htmlspecialchars($p['nombre']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categoria_nombre']) ?></span></td>
                                            <td>
                                                <?php if (!empty($p['precio_oferta']) && $p['precio_oferta'] > 0): ?>
                                                    <span class="text-muted text-decoration-line-through small"><?= number_format($p['precio'], 2) ?> €</span><br>
                                                    <span class="text-danger fw-bold"><?= number_format($p['precio_oferta'], 2) ?> €</span>
                                                <?php else: ?>
                                                    <span class="text-primary fw-bold"><?= number_format($p['precio'], 2) ?> €</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $p['stock'] <= 5 ? 'danger' : ($p['stock'] <= 15 ? 'warning text-dark' : 'success') ?>">
                                                    <?= $p['stock'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?= $p['destacado'] ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($p['precio_oferta']) && $p['precio_oferta'] > 0): ?>
                                                    <i class="bi bi-tag-fill text-danger" title="<?= number_format($p['precio_oferta'],2) ?> €"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-tag text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $p['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>' ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1"
                                                        onclick="editarProducto(<?= htmlspecialchars(json_encode($p)) ?>)"
                                                        data-bs-toggle="modal" data-bs-target="#modalProducto"
                                                        title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= BASE_URL ?>/?action=admin_eliminar_producto&id=<?= $p['id'] ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('¿Eliminar este producto?')"
                                                   title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No hay productos. <a href="#" data-bs-toggle="modal" data-bs-target="#modalProducto">Crear el primero</a>.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalProductoTitulo">
                    <i class="bi bi-box-seam me-2"></i>Nuevo producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/?action=admin_guardar_producto"
                  method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- ID oculto para edición -->
                    <input type="hidden" name="id" id="prod_id" value="0">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" id="prod_nombre"
                                   class="form-control" required maxlength="200">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <select name="id_categoria" id="prod_categoria" class="form-select" required>
                                <option value="">-- Selecciona --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea name="descripcion" id="prod_descripcion"
                                      class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Precio (€) *</label>
                            <input type="number" name="precio" id="prod_precio"
                                   class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock *</label>
                            <input type="number" name="stock" id="prod_stock"
                                   class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4 d-flex flex-column gap-2 justify-content-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="destacado" id="prod_destacado" value="1">
                                <label class="form-check-label fw-semibold" for="prod_destacado">
                                    <i class="bi bi-star-fill text-warning me-1"></i>Destacado
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="en_oferta" id="prod_en_oferta" value="1"
                                       onchange="togglePrecioOferta(this.checked)">
                                <label class="form-check-label fw-semibold" for="prod_en_oferta">
                                    <i class="bi bi-tag-fill text-danger me-1"></i>En oferta
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4" id="campo_precio_oferta" style="display:none;">
                            <label class="form-label fw-semibold text-danger">
                                <i class="bi bi-tag-fill me-1"></i>Precio de oferta (€)
                            </label>
                            <input type="number" name="precio_oferta" id="prod_precio_oferta"
                                   class="form-control border-danger" step="0.01" min="0"
                                   placeholder="0.00">
                            <div class="form-text text-danger">Debe ser menor que el precio normal.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Imagen del producto</label>
                            <input type="file" name="imagen" id="prod_imagen"
                                   class="form-control" accept="image/*">
                            <div class="form-text">Formatos: JPG, PNG, WEBP. Si no subes imagen se usará la predeterminada.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">
                        <i class="bi bi-save me-1"></i>Guardar producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePrecioOferta(show) {
    const campo = document.getElementById('campo_precio_oferta');
    const input = document.getElementById('prod_precio_oferta');
    campo.style.display = show ? 'block' : 'none';
    if (!show) input.value = '';
}

function limpiarFormProducto() {
    document.getElementById('modalProductoTitulo').innerHTML = '<i class="bi bi-box-seam me-2"></i>Nuevo producto';
    document.getElementById('prod_id').value = '0';
    document.getElementById('prod_nombre').value = '';
    document.getElementById('prod_descripcion').value = '';
    document.getElementById('prod_precio').value = '';
    document.getElementById('prod_stock').value = '';
    document.getElementById('prod_categoria').value = '';
    document.getElementById('prod_destacado').checked = false;
    document.getElementById('prod_en_oferta').checked = false;
    document.getElementById('prod_precio_oferta').value = '';
    togglePrecioOferta(false);
}

function editarProducto(p) {
    document.getElementById('modalProductoTitulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar producto';
    document.getElementById('prod_id').value = p.id;
    document.getElementById('prod_nombre').value = p.nombre;
    document.getElementById('prod_descripcion').value = p.descripcion || '';
    document.getElementById('prod_precio').value = p.precio;
    document.getElementById('prod_stock').value = p.stock;
    document.getElementById('prod_categoria').value = p.id_categoria;
    document.getElementById('prod_destacado').checked = p.destacado == 1;

    const tieneOferta = p.precio_oferta !== null && p.precio_oferta !== '' && parseFloat(p.precio_oferta) > 0;
    document.getElementById('prod_en_oferta').checked = tieneOferta;
    document.getElementById('prod_precio_oferta').value = tieneOferta ? p.precio_oferta : '';
    togglePrecioOferta(tieneOferta);
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>