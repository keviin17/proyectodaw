<?php
// controllers/OrderController.php

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class OrderController
{
    private Order $orderModel;
    private Cart  $cartModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->cartModel  = new Cart();
    }

    /** Muestra la página de checkout */
    public function checkout(): void
    {
        $this->requireLogin();
        $idUsuario = $_SESSION['usuario_id'];
        $items     = $this->cartModel->getByUsuario($idUsuario);

        if (empty($items)) {
            $_SESSION['error'] = 'Tu carrito está vacío.';
            header('Location: ' . BASE_URL . '/?action=carrito');
            exit;
        }

        $total = array_sum(array_column($items, 'subtotal'));
        require __DIR__ . '/../views/shop/checkout.php';
    }

    /** Procesa el pedido (POST desde checkout) */
    public function confirmar(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/?action=checkout');
            exit;
        }

        $idUsuario  = $_SESSION['usuario_id'];
        $direccion  = trim($_POST['direccion_envio'] ?? '');
        $metodoPago = trim($_POST['metodo_pago'] ?? 'tarjeta');
        $notas      = trim($_POST['notas'] ?? '');

        if (empty($direccion)) {
            $_SESSION['error'] = 'La dirección de envío es obligatoria.';
            header('Location: ' . BASE_URL . '/?action=checkout');
            exit;
        }

        $items = $this->cartModel->getByUsuario($idUsuario);

        if (empty($items)) {
            $_SESSION['error'] = 'Tu carrito está vacío.';
            header('Location: ' . BASE_URL . '/?action=carrito');
            exit;
        }

        // M7 — Verificar stock real en el momento de confirmar
        require_once __DIR__ . '/../models/Product.php';
        $productModel = new Product();
        foreach ($items as $item) {
            $prod = $productModel->getById($item['id_producto']);
            if (!$prod || $prod['stock'] < $item['cantidad']) {
                $_SESSION['error'] = 'El producto "' . htmlspecialchars($item['nombre']) . '" no tiene stock suficiente.';
                header('Location: ' . BASE_URL . '/?action=carrito');
                exit;
            }
        }

        $total = array_sum(array_column($items, 'subtotal'));

        // Crear el pedido en BD
        $idPedido = $this->orderModel->crear(
            $idUsuario, $items, $total, $direccion, $metodoPago, $notas
        );

        // Vaciar el carrito
        $this->cartModel->vaciar($idUsuario);

        $_SESSION['success'] = '¡Pedido #' . $idPedido . ' realizado con éxito! Te avisaremos cuando sea enviado.';
        header('Location: ' . BASE_URL . '/?action=mis_pedidos');
        exit;
    }

    /** Historial de pedidos del usuario */
    public function historial(): void
    {
        $this->requireLogin();
        $idUsuario = $_SESSION['usuario_id'];
        $pedidos   = $this->orderModel->getByUsuario($idUsuario);
        require __DIR__ . '/../views/user/orders.php';
    }

    /** Detalle de un pedido */
    public function detalle(int $id): void
    {
        $this->requireLogin();
        $idUsuario = $_SESSION['usuario_id'];
        $rol       = $_SESSION['usuario_rol'] ?? 'cliente';

        // Admin puede ver cualquier pedido; cliente solo los suyos
        if ($rol === 'admin') {
            $pedido = $this->orderModel->getById($id);
        } else {
            $pedido = $this->orderModel->getById($id, $idUsuario);
        }

        if (!$pedido) {
            http_response_code(404);
            echo '<h2 style="text-align:center;margin-top:60px;">Pedido no encontrado.</h2>';
            return;
        }

        $lineas = $this->orderModel->getDetalle($id);
        require __DIR__ . '/../views/user/orders.php';
    }

    private function requireLogin(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/?action=login');
            exit;
        }
    }
}
