<?php
// public/index.php
// Punto de entrada y router de la aplicación Velora Shop

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

// Configurar cookies de sesión más seguras
ini_set('session.cookie_httponly', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// C4 — Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// C4 — Función helper para verificar CSRF en POST
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
    }
}

// Cargar controladores
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/WishlistController.php';
require_once __DIR__ . '/../controllers/AdminController.php';

// Cargar modelos
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../models/Review.php';

// M5 — Verificar que el usuario activo sigue activo en BD (en cada request)
if (!empty($_SESSION['usuario_id'])) {
    $userCheck = new User();
    $uCheck = $userCheck->getById((int)$_SESSION['usuario_id']);
    if (!$uCheck || !$uCheck['activo']) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ' . BASE_URL . '/?action=login');
        exit;
    }
}

// Leer la acción de la URL: ?action=algo
$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'home':
    case '':
        $ctrl = new ProductController();
        $ctrl->catalogo();
        break;

    case 'catalogo':
        $ctrl = new ProductController();
        $genero = trim($_GET['genero'] ?? '');
        if ($genero !== '') {
            $ctrl->catalogoPorGenero($genero);
        } else {
            $ctrl->catalogo();
        }
        break;

    case 'buscar':
        $ctrl = new ProductController();
        $ctrl->buscar();
        break;

    case 'destacados':
        $ctrl = new ProductController();
        $ctrl->destacados();
        break;

    case 'producto':
        $ctrl = new ProductController();
        $id = (int) ($_GET['id'] ?? 0);
        $ctrl->detalle($id);
        break;

    case 'valorar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new ProductController();
        $ctrl->valorar();
        break;

    case 'register':
        $ctrl = new AuthController();
        $ctrl->registrar();
        break;

    case 'login':
        $ctrl = new AuthController();
        $ctrl->login();
        break;

    case 'logout':
        $ctrl = new AuthController();
        $ctrl->logout();
        break;

    case 'carrito':
        $ctrl = new CartController();
        $ctrl->mostrar();
        break;

    case 'carrito_anadir':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new CartController();
        $ctrl->anadir();
        break;

    case 'carrito_actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new CartController();
        $ctrl->actualizar();
        break;

    case 'carrito_eliminar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new CartController();
        $ctrl->eliminar();
        break;

    case 'checkout':
        $ctrl = new OrderController();
        $ctrl->checkout();
        break;

    case 'confirmar_pedido':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new OrderController();
        $ctrl->confirmar();
        break;

    case 'mis_pedidos':
        $ctrl = new OrderController();
        $ctrl->historial();
        break;

    case 'pedido_detalle':
        $id = (int) ($_GET['id'] ?? 0);
        // Si viene con ?formato=json devuelve JSON para el modal del admin
        if (($_GET['formato'] ?? '') === 'json') {
            // A2 — Verificar que es admin antes de devolver datos JSON
            if (empty($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'forbidden']);
                exit;
            }
            $orderModel = new Order();
            $pedido     = $orderModel->getById($id);
            $lineas     = $orderModel->getDetalle($id);
            header('Content-Type: application/json');
            if (!$pedido) {
                echo json_encode(['error' => true]);
            } else {
                $userModel = new User();
                $cliente   = $userModel->getById($pedido['id_usuario']);
                $pedido['cliente_nombre'] = $cliente['nombre'] ?? '—';
                $pedido['cliente_email']  = $cliente['email']  ?? '—';
                echo json_encode(['pedido' => $pedido, 'lineas' => $lineas]);
            }
            exit;
        }
        $ctrl = new OrderController();
        $ctrl->detalle($id);
        break;

    case 'wishlist_toggle':
        // AJAX toggle wishlist (sin recarga de página)
        header('Content-Type: application/json');
        if (empty($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'no_login']);
            exit;
        }
        $idProd = (int)($_POST['id_producto'] ?? 0);
        if ($idProd <= 0) {
            echo json_encode(['error' => 'invalid']);
            exit;
        }
        $wl = new Wishlist();
        if ($wl->existe($_SESSION['usuario_id'], $idProd)) {
            $wl->eliminar($_SESSION['usuario_id'], $idProd);
            echo json_encode(['status' => 'removed']);
        } else {
            $wl->anadir($_SESSION['usuario_id'], $idProd);
            echo json_encode(['status' => 'added']);
        }
        exit;

    case 'wishlist':
        $ctrl = new WishlistController();
        $ctrl->mostrar();
        break;

    case 'wishlist_anadir':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new WishlistController();
        $ctrl->anadir();
        break;

    case 'wishlist_eliminar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new WishlistController();
        $ctrl->eliminar();
        break;

    case 'perfil':
        require_login_redirect();
        $userModel = new User();
        $usuario   = $userModel->getById($_SESSION['usuario_id']);
        require __DIR__ . '/../views/user/profile.php';
        break;

    case 'perfil_guardar':
        require_login_redirect();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $userModel = new User();
            $datos = [
                'nombre'        => trim($_POST['nombre'] ?? ''),
                'telefono'      => trim($_POST['telefono'] ?? ''),
                'direccion'     => trim($_POST['direccion'] ?? ''),
                'ciudad'        => trim($_POST['ciudad'] ?? ''),
                'codigo_postal' => trim($_POST['codigo_postal'] ?? ''),
            ];
            $userModel->actualizarPerfil($_SESSION['usuario_id'], $datos);
            $_SESSION['usuario_nombre']    = $datos['nombre'];
            $_SESSION['usuario_direccion'] = $datos['direccion'];
            $_SESSION['success'] = 'Perfil actualizado correctamente.';
        }
        header('Location: ' . BASE_URL . '/?action=perfil');
        exit;

    case 'cambiar_contrasena':
        require_login_redirect();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $passActual = $_POST['pass_actual'] ?? '';
            $passNueva  = $_POST['pass_nueva']  ?? '';
            $passNueva2 = $_POST['pass_nueva2'] ?? '';

            $userModel = new User();
            $usuario   = $userModel->getById($_SESSION['usuario_id']);

            if (!$usuario || !password_verify($passActual, $usuario['contraseña'])) {
                $_SESSION['error'] = 'La contraseña actual es incorrecta.';
            } elseif (strlen($passNueva) < 6) {
                $_SESSION['error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
            } elseif ($passNueva !== $passNueva2) {
                $_SESSION['error'] = 'Las contraseñas nuevas no coinciden.';
            } else {
                $hash = password_hash($passNueva, PASSWORD_BCRYPT);
                $userModel->cambiarContrasena($_SESSION['usuario_id'], $hash);
                $_SESSION['success'] = 'Contraseña actualizada correctamente.';
            }
        }
        header('Location: ' . BASE_URL . '/?action=perfil');
        exit;

    case 'admin_dashboard':
        $ctrl = new AdminController();
        $ctrl->dashboard();
        break;

    case 'admin_productos':
        $ctrl = new AdminController();
        $ctrl->listarProductos();
        break;

    case 'admin_guardar_producto':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new AdminController();
        $ctrl->guardarProducto();
        break;

    case 'admin_eliminar_producto':
        $ctrl = new AdminController();
        $ctrl->eliminarProducto();
        break;

    case 'admin_pedidos':
        $ctrl = new AdminController();
        $ctrl->listarPedidos();
        break;

    case 'admin_cambiar_estado':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new AdminController();
        $ctrl->cambiarEstadoPedido();
        break;

    case 'admin_usuarios':
        $ctrl = new AdminController();
        $ctrl->listarUsuarios();
        break;

    case 'admin_toggle_usuario':
        $ctrl = new AdminController();
        $ctrl->toggleUsuario();
        break;

    case 'admin_cambiar_rol':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
        }
        $ctrl = new AdminController();
        $ctrl->cambiarRol();
        break;

    default:
        http_response_code(404);
        echo '<div style="font-family:sans-serif;text-align:center;margin-top:80px;">';
        echo '<h1>404 - Página no encontrada</h1>';
        echo '<a href="' . BASE_URL . '/">Volver al inicio</a>';
        echo '</div>';
        break;
}

function require_login_redirect(): void
{
    if (empty($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . '/?action=login');
        exit;
    }
}
