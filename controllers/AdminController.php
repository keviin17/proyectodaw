<?php
// controllers/AdminController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminController
{
    // Verifica que el usuario sea administrador
    private function requireAdmin(): void
    {
        if (
            empty($_SESSION['usuario_rol']) ||
            $_SESSION['usuario_rol'] !== 'admin'
        ) {
            http_response_code(403);
            die("Acceso denegado.");
        }
    }

    // ── DASHBOARD ────────────────────────────────────────────

    public function dashboard(): void
    {
        $this->requireAdmin();
        $pdo = getConnection();

        // Estadísticas generales
        $stats = [
            'total_usuarios'  => (int) $pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn(),
            'total_productos' => (int) $pdo->query("SELECT COUNT(*) FROM producto WHERE activo=1")->fetchColumn(),
            'total_pedidos'   => (int) $pdo->query("SELECT COUNT(*) FROM pedido")->fetchColumn(),
            'ingresos_totales' => (float) $pdo->query(
                "SELECT COALESCE(SUM(total),0) FROM pedido WHERE estado NOT IN ('cancelado')"
            )->fetchColumn(),
        ];

        // Últimos 5 pedidos
        $ultimosPedidos = $pdo->query(
            "SELECT p.*, u.nombre AS cliente_nombre
             FROM pedido p
             JOIN usuario u ON p.id_usuario = u.id
             ORDER BY p.fecha DESC
             LIMIT 5"
        )->fetchAll();

        // Productos con stock bajo (< 10)
        $productosBajoStock = $pdo->query(
            "SELECT nombre, stock FROM producto
             WHERE activo = 1 AND stock < 10
             ORDER BY stock ASC
             LIMIT 8"
        )->fetchAll();

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // ── PRODUCTOS ────────────────────────────────────────────

    public function listarProductos(): void
    {
        $this->requireAdmin();
        $pdo = getConnection();

        // Cargar categorías primero para que siempre esté disponible en la vista
        $categorias = $pdo->query("SELECT * FROM categoria WHERE activo=1 ORDER BY nombre ASC")->fetchAll();

        $q           = trim($_GET['q'] ?? '');
        $idCategoria = (int) ($_GET['categoria'] ?? 0);
        $limit       = 15;
        $page        = max(1, (int) ($_GET['pagina'] ?? 1));
        $offset      = ($page - 1) * $limit;

        $product = new Product();
        if ($q !== '') {
            $productos = $product->buscarAdmin($q, $idCategoria);
            $total     = count($productos);
            $paginas   = 1;
            $page      = 1;
        } else {
            $total     = $product->contarAdmin($idCategoria);
            $paginas   = (int) ceil($total / $limit);
            $productos = $product->getAllAdmin($limit, $offset, $idCategoria);
        }

        // Exportar al scope global para garantizar visibilidad en la vista
        $GLOBALS['categorias']   = $categorias;
        $GLOBALS['productos']    = $productos;
        $GLOBALS['paginas']      = $paginas;
        $GLOBALS['page']         = $page;
        $GLOBALS['total']        = $total;
        $GLOBALS['idCategoria']  = $idCategoria;

        require __DIR__ . '/../views/admin/products.php';
    }

    public function guardarProducto(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/?action=admin_productos');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        // Calcular precio_oferta: solo si el checkbox "en_oferta" está marcado y hay valor
        $enOferta    = isset($_POST['en_oferta']);
        $precioOferta = ($enOferta && isset($_POST['precio_oferta']) && $_POST['precio_oferta'] !== '')
                        ? (float) $_POST['precio_oferta']
                        : null;

        $datos = [
            'nombre'        => trim($_POST['nombre'] ?? ''),
            'descripcion'   => trim($_POST['descripcion'] ?? ''),
            'precio'        => (float) ($_POST['precio'] ?? 0),
            'precio_oferta' => $precioOferta,
            'stock'         => (int) ($_POST['stock'] ?? 0),
            'id_categoria'  => (int) ($_POST['id_categoria'] ?? 0),
            'destacado'     => isset($_POST['destacado']) ? 1 : 0,
            'talla'         => trim($_POST['talla'] ?? '') ?: null,
        ];

        // M1 — Validar que precio_oferta sea menor que precio
        if ($precioOferta !== null && $precioOferta >= $datos['precio']) {
            $_SESSION['error'] = 'El precio de oferta debe ser menor que el precio normal.';
            header('Location: ' . BASE_URL . '/?action=admin_productos');
            exit;
        }

        // C3 — Manejo de imagen con validación de extensión y MIME
        if (!empty($_FILES['imagen']['name'])) {
            $extPermitidas  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $mimePermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            $ext  = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $mime = mime_content_type($_FILES['imagen']['tmp_name']);

            if (!in_array($ext, $extPermitidas) || !in_array($mime, $mimePermitidos)) {
                $_SESSION['error'] = 'Solo se permiten imágenes JPG, PNG, WEBP o GIF.';
                header('Location: ' . BASE_URL . '/?action=admin_productos');
                exit;
            }

            // Determinar subcarpeta según el género de la categoría seleccionada
            $pdo2       = getConnection();
            $stmtCat    = $pdo2->prepare("SELECT genero FROM categoria WHERE id = ?");
            $stmtCat->execute([$datos['id_categoria']]);
            $generoCateg = $stmtCat->fetchColumn() ?: 'unisex';

            // Mapa de género a nombre de carpeta (niño usa "nino" sin tilde en disco)
            $carpetaMap = [
                'hombre' => 'hombre',
                'mujer'  => 'mujer',
                'niño'   => 'nino',
                'unisex' => 'unisex',
            ];
            $subcarpeta = $carpetaMap[$generoCateg] ?? 'unisex';
            $dirDestino = __DIR__ . "/../assets/img/products/{$subcarpeta}";

            // Asegurar que la carpeta existe
            if (!is_dir($dirDestino)) {
                mkdir($dirDestino, 0775, true);
            }

            // Nombre de archivo = nombre del producto (slug seguro) + extensión
            $nombreSlug = preg_replace('/[^a-z0-9_-]/i', '_', $datos['nombre']);
            $nombreSlug = strtolower(trim($nombreSlug, '_'));
            $nombreImg  = $nombreSlug . '.' . $ext;
            $rutaDestino = $dirDestino . '/' . $nombreImg;

            // Avisar si ya existe un archivo con ese nombre
            if (file_exists($rutaDestino)) {
                $_SESSION['error'] = "Ya existe una imagen llamada «{$nombreImg}» en la carpeta /{$subcarpeta}/. "
                    . "Renombra el archivo o elimina el existente antes de continuar.";
                header('Location: ' . BASE_URL . '/?action=admin_productos');
                exit;
            }

            move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);

            // Guardar en BD como "subcarpeta/nombre.ext" para rutas relativas correctas
            $datos['imagen'] = $subcarpeta . '/' . $nombreImg;

        } elseif ($id > 0) {
            // Al editar sin subir imagen nueva, conservar la imagen actual en BD
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT imagen FROM producto WHERE id = ?");
            $stmt->execute([$id]);
            $datos['imagen'] = $stmt->fetchColumn() ?: null;
        }

        $product = new Product();
        if ($id > 0) {
            $product->actualizar($id, $datos);
            $_SESSION['success'] = "Producto actualizado correctamente.";
        } else {
            // M2 — Capturar duplicado de nombre
            $result = $product->crear($datos);
            if ($result === -1) {
                $_SESSION['error'] = 'Ya existe un producto con ese nombre.';
                header('Location: ' . BASE_URL . '/?action=admin_productos');
                exit;
            }
            $_SESSION['success'] = "Producto creado correctamente.";
        }
        header('Location: ' . BASE_URL . '/?action=admin_productos');
        exit;
    }

    public function eliminarProducto(): void
    {
        $this->requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $product = new Product();
        $product->eliminar($id);
        $_SESSION['success'] = "Producto eliminado.";
        header('Location: ' . BASE_URL . '/?action=admin_productos');
        exit;
    }

    // ── PEDIDOS ──────────────────────────────────────────────

    public function listarPedidos(): void
    {
        $this->requireAdmin();
        $orderModel = new Order();
        $pedidos    = $orderModel->getAll();
        require __DIR__ . '/../views/admin/orders.php';
    }

    public function cambiarEstadoPedido(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/?action=admin_pedidos');
            exit;
        }

        $idPedido = (int) ($_POST['id_pedido'] ?? 0);
        $estado   = trim($_POST['estado'] ?? '');

        $estadosValidos = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'];
        if ($idPedido > 0 && in_array($estado, $estadosValidos)) {
            $orderModel = new Order();
            $orderModel->cambiarEstado($idPedido, $estado);
            $_SESSION['success'] = 'Estado del pedido #' . $idPedido . ' actualizado a ' . $estado . '.';
        } else {
            $_SESSION['error'] = "Datos inválidos.";
        }
        header('Location: ' . BASE_URL . '/?action=admin_pedidos');
        exit;
    }

    // ── USUARIOS ─────────────────────────────────────────────

    public function listarUsuarios(): void
    {
        $this->requireAdmin();
        $userModel = new User();
        $usuarios  = $userModel->getAll();
        require __DIR__ . '/../views/admin/users.php';
    }

    public function toggleUsuario(): void
    {
        $this->requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);

        // No se puede desactivar a uno mismo
        if ($id === (int) $_SESSION['usuario_id']) {
            $_SESSION['error'] = "No puedes desactivar tu propia cuenta.";
            header('Location: ' . BASE_URL . '/?action=admin_usuarios');
            exit;
        }

        if ($id > 0) {
            $userModel = new User();
            $userModel->toggleActivo($id);
            $_SESSION['success'] = "Estado del usuario actualizado.";
        }
        header('Location: ' . BASE_URL . '/?action=admin_usuarios');
        exit;
    }

    public function cambiarRol(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/?action=admin_usuarios');
            exit;
        }

        $id  = (int) ($_POST['id'] ?? 0);
        $rol = trim($_POST['rol'] ?? '');

        // No se puede cambiar el rol de uno mismo
        if ($id === (int) $_SESSION['usuario_id']) {
            $_SESSION['error'] = "No puedes cambiar tu propio rol.";
            header('Location: ' . BASE_URL . '/?action=admin_usuarios');
            exit;
        }

        if ($id > 0 && in_array($rol, ['cliente', 'admin'])) {
            $userModel = new User();
            $userModel->cambiarRol($id, $rol);
            $_SESSION['success'] = 'Rol del usuario actualizado a ' . $rol . '.';
        } else {
            $_SESSION['error'] = "Datos inválidos.";
        }
        header('Location: ' . BASE_URL . '/?action=admin_usuarios');
        exit;
    }
}