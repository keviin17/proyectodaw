<?php
// controllers/WishlistController.php

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/Wishlist.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class WishlistController
{
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new Wishlist();
    }

    /** Muestra la lista de deseos del usuario */
    public function mostrar(): void
    {
        $this->requireLogin();
        $idUsuario = $_SESSION['usuario_id'];
        $productos = $this->wishlistModel->getByUsuario($idUsuario);
        require __DIR__ . '/../views/shop/wishlist.php';
    }

    /** Añade un producto a la lista de deseos */
    public function anadir(): void
    {
        $this->requireLogin();
        $idProducto = (int) ($_POST['id_producto'] ?? $_GET['id_producto'] ?? 0);

        if ($idProducto > 0) {
            $this->wishlistModel->anadir($_SESSION['usuario_id'], $idProducto);
        }

        // A3 — Validar redirect para evitar Open Redirect
        $redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? '');
        if (empty($redirect) || (!str_starts_with($redirect, BASE_URL) && !str_starts_with($redirect, '/'))) {
            $redirect = BASE_URL . '/?action=wishlist';
        }
        header('Location: ' . $redirect);
        exit;
    }

    /** Elimina un producto de la lista de deseos */
    public function eliminar(): void
    {
        $this->requireLogin();
        $idProducto = (int) ($_POST['id_producto'] ?? $_GET['id_producto'] ?? 0);

        if ($idProducto > 0) {
            $this->wishlistModel->eliminar($_SESSION['usuario_id'], $idProducto);
        }

        header('Location: ' . BASE_URL . '/?action=wishlist');
        exit;
    }

    private function requireLogin(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/?action=login');
            exit;
        }
    }
}
