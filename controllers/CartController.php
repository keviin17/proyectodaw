<?php
// controllers/CartController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CartController
{
    private Cart $cartModel;

    public function __construct()
    {
        $this->cartModel = new Cart();
    }

    // Verificar que el usuario esta logueado
    private function requireLogin(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/?action=login');
            exit;
        }
    }

    // Anadir producto al carrito
    public function anadir(): void
    {
        $this->requireLogin();

        $idProducto = (int) ($_POST['id_producto'] ?? 0);
        $cantidad   = (int) ($_POST['cantidad'] ?? 1);
        $talla      = trim($_POST['talla'] ?? '');
        $idUsuario  = $_SESSION['usuario_id'];

        if ($idProducto <= 0 || $cantidad <= 0) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $this->cartModel->anadir($idUsuario, $idProducto, $cantidad, $talla);

        header('Location: ' . BASE_URL . '/?action=carrito');
        exit;
    }

    // Actualizar cantidad de una linea del carrito
    public function actualizar(): void
    {
        $this->requireLogin();

        $idCarrito = (int) ($_POST['id_carrito'] ?? 0);
        $cantidad  = (int) ($_POST['cantidad'] ?? 1);
        $idUsuario = $_SESSION['usuario_id'];

        if ($cantidad <= 0) {
            // Si cantidad es 0 o negativa, eliminar la linea
            $this->cartModel->eliminarLinea($idCarrito, $idUsuario);
        } else {
            $this->cartModel->actualizar($idCarrito, $idUsuario, $cantidad);
        }

        header('Location: ' . BASE_URL . '/?action=carrito');
        exit;
    }

    // Eliminar una linea del carrito
    public function eliminar(): void
    {
        $this->requireLogin();

        $idCarrito = (int) ($_POST['id_carrito'] ?? 0);
        $idUsuario = $_SESSION['usuario_id'];

        $this->cartModel->eliminarLinea($idCarrito, $idUsuario);

        header('Location: ' . BASE_URL . '/?action=carrito');
        exit;
    }

    // Mostrar pagina del carrito
    public function mostrar(): void
    {
        $this->requireLogin();

        $idUsuario = $_SESSION['usuario_id'];
        $items     = $this->cartModel->getByUsuario($idUsuario);
        $total     = array_sum(array_column($items, 'subtotal'));

        require __DIR__ . '/../views/shop/cart.php';
    }
}
